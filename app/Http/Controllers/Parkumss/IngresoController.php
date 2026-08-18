<?php

namespace App\Http\Controllers\Parkumss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Espacio;
use App\Models\Pago;
use App\Models\RegistroIngreso;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\DB;

class IngresoController extends Controller
{
    /**
     * Pantalla principal de ventanilla: entradas y salidas.
     * El Global Scope ya filtra todo por el parqueo del
     * encargado logueado, no hace falta filtrar aqui.
     */
    public function index()
    {
        $activos = RegistroIngreso::activos()
            ->with(['vehiculo', 'espacio', 'tarjeta.usuario'])
            ->orderBy('hora_ingreso')
            ->get();
 
        // Cobro estimado en vivo: lo que se debe si sale ahora.
        // Asi el encargado nunca cobra de menos.
        $activos->each(fn ($r) => $r->estimado = $r->cobroEstimado());
 
        return view('parkumss.ingresos.index', [
            'activos'  => $activos,
            'libres'   => Espacio::libres()->orderBy('numero')->get(),
            'parqueo'  => auth()->user()->parqueoAsignado,
        ]);
    }

    /**
     * Al escanear la tarjeta: devuelve el vehiculo, el saldo y
     * los espacios libres DEL MISMO TIPO que el vehiculo.
     */
    public function buscar(Request $request)
    {
        $tarjeta = Tarjeta::with('usuario.vehiculos')
                          ->where('codigo_rfid', $request->codigo_rfid)
                          ->firstOrFail();
 
        $vehiculo = $tarjeta->usuario->vehiculos()->where('activo', 1)->first();
 
        if (! $vehiculo) {
            return response()->json(['error' => 'El usuario no tiene vehiculo registrado.'], 422);
        }
 
        $espacios = Espacio::where('tipo', $vehiculo->tipo)
                           ->where('estado', 'libre')
                           ->orderBy('numero')
                           ->get(['id', 'numero']);
 
        return response()->json([
            'tarjeta_id' => $tarjeta->id,
            'saldo'      => $tarjeta->saldo,
            'espacios'   => $espacios,
            'vehiculo'   => $vehiculo->only(['id', 'placa', 'tipo']) + [
                'usuario_nombre' => $tarjeta->usuario->nombre_completo,
            ],
        ]);
    }

    /**
     * ENTRADA: valida tipo y estado, abre el registro
     * y marca el espacio como ocupado.
     */
    public function entrada(Request $request)
    {
        $datos = $request->validate([
            'tarjeta_id'  => ['required', 'exists:tarjetas,id'],
            'vehiculo_id' => ['required', 'exists:vehiculos,id'],
            'espacio_id'  => ['required', 'exists:espacios,id'],
        ]);
 
        $tarjeta  = Tarjeta::findOrFail($datos['tarjeta_id']);
        $vehiculo = $tarjeta->usuario->vehiculos()->findOrFail($datos['vehiculo_id']);
        $espacio  = Espacio::findOrFail($datos['espacio_id']);
 
        if ($tarjeta->estado !== 'activa') {
            return back()->withErrors('La tarjeta esta bloqueada.');
        }
 
        if ($espacio->estado !== 'libre') {
            return back()->withErrors("El espacio {$espacio->numero} ya esta ocupado.");
        }
 
        if ($espacio->tipo !== $vehiculo->tipo) {
            return back()->withErrors("El espacio {$espacio->numero} es para {$espacio->tipo}.");
        }
 
        $adentro = RegistroIngreso::where('vehiculo_id', $vehiculo->id)
                                  ->where('estado', 'activo')
                                  ->exists();
 
        if ($adentro) {
            return back()->withErrors("El vehiculo {$vehiculo->placa} ya esta dentro.");
        }
 
        DB::transaction(function () use ($tarjeta, $vehiculo, $espacio) {
            RegistroIngreso::create([
                'tarjeta_id'   => $tarjeta->id,
                'vehiculo_id'  => $vehiculo->id,
                'espacio_id'   => $espacio->id,
                'hora_ingreso' => now(),
                'estado'       => 'activo',
            ]);
 
            $espacio->update(['estado' => 'ocupado']);
        });
 
        return back()->with('exito', 'Ingreso registrado.');
    }

    /**
     * ENTRADA MANUAL: visitante sin tarjeta.
     * Mismo flujo que `entrada`, pero en vez de tarjeta y
     * vehiculo registrado guarda placa y tipo directamente.
     * Pagara en efectivo al salir.
     */
    public function entradaVisitante(Request $request)
    {
        $datos = $request->validate([
            'placa_visitante' => ['required', 'string', 'max:15'],
            'tipo_visitante'  => ['required', 'in:moto,auto'],
            'espacio_id'      => ['required', 'exists:espacios,id'],
        ]);
 
        $placa   = strtoupper(trim($datos['placa_visitante']));
        $espacio = Espacio::findOrFail($datos['espacio_id']);
 
        if ($espacio->estado !== 'libre') {
            return back()->withErrors("El espacio {$espacio->numero} ya esta ocupado.");
        }
 
        // TIPO: el espacio debe corresponder al tipo declarado.
        if ($espacio->tipo !== $datos['tipo_visitante']) {
            return back()->withErrors("El espacio {$espacio->numero} es para {$espacio->tipo}.");
        }
 
        // Esa placa no puede estar ya dentro, ni como visitante
        // ni como vehiculo registrado.
        $adentro = RegistroIngreso::activos()
            ->where(function ($q) use ($placa) {
                $q->where('placa_visitante', $placa)
                  ->orWhereHas('vehiculo', fn($v) => $v->where('placa', $placa));
            })
            ->exists();
 
        if ($adentro) {
            return back()->withErrors("El vehiculo {$placa} ya esta dentro.");
        }
 
        DB::transaction(function () use ($datos, $placa, $espacio) {
            RegistroIngreso::create([
                'placa_visitante' => $placa,
                'tipo_visitante'  => $datos['tipo_visitante'],
                'espacio_id'      => $espacio->id,
                'hora_ingreso'    => now(),
                'estado'          => 'activo',
            ]);
 
            $espacio->update(['estado' => 'ocupado']);
        });
 
        return back()->with('exito', "Ingreso manual registrado: {$placa}.");
    }

    /**
     * SALIDA: cobra, cierra el registro y libera el espacio.
     * Funciona igual para clientes y visitantes; lo unico que
     * cambia es si se descuenta saldo o se cobra en efectivo.
     */
    public function salida(RegistroIngreso $registro)
    {
        if ($registro->estado !== 'activo') {
            return back()->withErrors('Este registro ya fue cerrado.');
        }
 
        $salida = now();
        $cobro  = $registro->cobroEstimado();
 
        // El calculo del cobro es identico en ambos casos.
        // Solo cambia de donde sale el dinero.
        $esVisitante = $registro->esVisitante();
        $tarjeta     = $registro->tarjeta;
 
        if (! $esVisitante && $tarjeta->saldo < $cobro['monto']) {
            return back()->withErrors(
                "Saldo insuficiente: debe {$cobro['monto']} Bs y tiene {$tarjeta->saldo} Bs. " .
                'Recargue la tarjeta o cobre en efectivo.'
            );
        }
 
        DB::transaction(function () use ($registro, $tarjeta, $cobro, $salida, $esVisitante) {
 
            $datosPago = [
                'registro_id' => $registro->id,
                'monto'       => $cobro['monto'],
                'fecha_pago'  => $salida,
                'estado'      => 'exitoso',
            ];
 
            if ($esVisitante) {
                // Efectivo: no hay tarjeta ni saldos que mover.
                $datosPago['metodo'] = 'efectivo';
            } else {
                $saldoAnterior = $tarjeta->saldo;
                $tarjeta->decrement('saldo', $cobro['monto']);
 
                $datosPago += [
                    'metodo'          => 'tarjeta',
                    'tarjeta_id'      => $tarjeta->id,
                    'saldo_anterior'  => $saldoAnterior,
                    'saldo_posterior' => $saldoAnterior - $cobro['monto'],
                ];
            }
 
            Pago::create($datosPago);
 
            $registro->update([
                'hora_salida'     => $salida,
                'periodos_usados' => $cobro['periodos'],
                'estado'          => 'finalizado',
            ]);
 
            $registro->espacio?->update(['estado' => 'libre']);
        });
 
        $metodo = $esVisitante ? 'en efectivo' : 'de la tarjeta';
 
        return back()->with(
            'exito',
            "Salida registrada: {$cobro['periodos']} periodo(s), " .
            "{$cobro['monto']} Bs cobrados {$metodo}."
        );
    }
}
