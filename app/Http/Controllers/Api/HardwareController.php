<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarjeta;
use App\Models\Espacio;
use App\Models\RegistroIngreso;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HardwareController extends Controller
{
    public function lectura(Request $request)
    {
        try {
            $datos = $request->validate([
                'codigo_rfid' => 'required|string',
                'parqueo_id' => 'required|integer',
                'dispositivo_id' => 'required|string',
            ]);

            // Guardar cualquier tarjeta escaneada para que la vista web pueda capturarla
            \Illuminate\Support\Facades\Cache::put('ultima_tarjeta_escaneada_' . $datos['parqueo_id'], $datos['codigo_rfid'], 60);

            // 1 & 2. Buscar tarjeta y verificar existencia
            $tarjeta = Tarjeta::with('usuario.vehiculos')
                ->where('codigo_rfid', $datos['codigo_rfid'])
                ->where('parqueo_id', $datos['parqueo_id'])
                ->first();

            if (!$tarjeta) {
                return response()->json([
                    'success' => true,
                    'acceso' => false,
                    'accion' => 'ninguna',
                    'codigo' => 'tarjeta_no_registrada',
                    'linea1' => 'Tarjeta invalida',
                    'linea2' => 'No registrada'
                ]);
            }

            // 3. Verificar que esté activa
            if (!$tarjeta->estaActiva()) {
                return response()->json([
                    'success' => true,
                    'acceso' => false,
                    'accion' => 'ninguna',
                    'codigo' => 'tarjeta_inactiva',
                    'linea1' => 'Tarjeta bloqueada',
                    'linea2' => 'Acceso denegado'
                ]);
            }

            // 4 & 5. Obtener usuario y vehiculo
            $usuario = $tarjeta->usuario;
            $vehiculo = $usuario->vehiculos()->where('activo', 1)->first();

            if (!$vehiculo) {
                return response()->json([
                    'success' => true,
                    'acceso' => false,
                    'accion' => 'ninguna',
                    'codigo' => 'sin_vehiculo',
                    'linea1' => 'Sin vehiculo',
                    'linea2' => 'Registre uno'
                ]);
            }

            // 6. Buscar si existe un registro_ingreso abierto
            $registroActivo = RegistroIngreso::activos()
                ->where('tarjeta_id', $tarjeta->id)
                ->where('parqueo_id', $datos['parqueo_id'])
                ->first();

            if (!$registroActivo) {
                return $this->procesarEntrada($tarjeta, $vehiculo, $datos['parqueo_id']);
            } else {
                return $this->procesarSalida($registroActivo, $tarjeta);
            }

        } catch (\Exception $e) {
            Log::error('Error en hardware/lectura: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'codigo' => 'server_error',
                'linea1' => 'Error servidor',
                'linea2' => 'Intente de nuevo'
            ], 500);
        }
    }

    private function procesarEntrada($tarjeta, $vehiculo, $parqueoId)
    {
        // 1. Validar que el vehiculo no esté ya adentro
        $placaDentro = RegistroIngreso::activos()
            ->where('parqueo_id', $parqueoId)
            ->where(function ($q) use ($vehiculo) {
                $q->where('placa_visitante', $vehiculo->placa)
                  ->orWhereHas('vehiculo', fn ($v) => $v->where('placa', $vehiculo->placa));
            })
            ->exists();

        if ($placaDentro) {
            return response()->json([
                'success' => true,
                'acceso' => false,
                'accion' => 'entrada',
                'codigo' => 'vehiculo_adentro',
                'linea1' => 'Placa ya dentro',
                'linea2' => 'Acceso denegado'
            ]);
        }

        // 4. Buscar un espacio libre compatible
        $espacio = Espacio::where('parqueo_id', $parqueoId)
            ->where('estado', 'libre')
            ->where('tipo', $vehiculo->tipo)
            ->first();

        // 5. Si no existe espacio compatible
        if (!$espacio) {
            return response()->json([
                'success' => true,
                'acceso' => false,
                'accion' => 'entrada',
                'codigo' => 'sin_espacio',
                'linea1' => 'Parqueo lleno',
                'linea2' => 'Sin espacio'
            ]);
        }

        // 6, 7. Crear el registro y marcar espacio ocupado
        DB::transaction(function () use ($tarjeta, $vehiculo, $espacio, $parqueoId) {
            RegistroIngreso::create([
                'parqueo_id'   => $parqueoId,
                'tarjeta_id'   => $tarjeta->id,
                'vehiculo_id'  => $vehiculo->id,
                'espacio_id'   => $espacio->id,
                'hora_ingreso' => now(),
                'estado'       => 'activo',
            ]);

            $espacio->ocupar();
        });

        // Aseguramos max 16 caracteres para la linea LCD
        $nombreCorto = substr($tarjeta->usuario->nombre, 0, 11);

        return response()->json([
            'success' => true,
            'acceso' => true,
            'accion' => 'entrada',
            'linea1' => "Hola $nombreCorto",
            'linea2' => "Espacio " . $espacio->numero
        ]);
    }

    private function procesarSalida($registro, $tarjeta)
    {
        $salida = now();
        $cobro = $registro->cobroEstimado();

        // 5. Si es tarjeta: verificar saldo
        if ($tarjeta->saldo < $cobro['monto']) {
            return response()->json([
                'success' => true,
                'acceso' => false,
                'accion' => 'salida',
                'codigo' => 'saldo_insuficiente',
                'linea1' => 'Saldo insuf.',
                'linea2' => 'Recargue'
            ]);
        }

        DB::transaction(function () use ($registro, $tarjeta, $cobro, $salida) {
            $anterior = $tarjeta->saldo;
            // 6. Si saldo suficiente: descontar
            $tarjeta->decrement('saldo', $cobro['monto']);

            // 7. Crear el pago
            Pago::create([
                'registro_id' => $registro->id,
                'tarjeta_id' => $tarjeta->id,
                'parqueo_id' => $registro->parqueo_id,
                'monto' => $cobro['monto'],
                'saldo_anterior' => $anterior,
                'saldo_posterior' => $anterior - $cobro['monto'],
                'metodo' => 'tarjeta',
                'estado' => 'exitoso',
                'fecha_pago' => $salida,
            ]);

            // 9. Actualizar el registro
            $registro->update([
                'hora_salida' => $salida,
                'periodos_usados' => $cobro['periodos'],
                'estado' => 'finalizado',
            ]);

            // 8. Liberar espacio
            $registro->espacio?->liberar();
        });

        // 10. Responder
        return response()->json([
            'success' => true,
            'acceso' => true,
            'accion' => 'salida',
            'monto' => (float) $cobro['monto'],
            'saldo' => (float) ($tarjeta->saldo),
            'linea1' => 'Salida OK',
            'linea2' => 'Bs ' . number_format($cobro['monto'], 2)
        ]);
    }
}
