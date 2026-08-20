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
     * Pantalla de registro de ingreso.
     */
    public function index()
    {
        // Se mandan TODOS los espacios, no solo los libres: la
        // grilla tambien muestra los ocupados con su placa.
        $espacios = Espacio::with('registroActivo.vehiculo')
                           ->orderBy('numero')
                           ->get();
 
        return view('ingresos.index', [
            'espacios'       => $espacios,
            'espaciosLibres' => $espacios->where('estado', 'libre'),
            'totalEspacios'  => $espacios->count(),
            'parqueo'        => auth()->user()->parqueoAsignado,
            // Solo para pruebas sin lector fisico. En produccion
            // config('app.debug') es false y la vista las oculta.
            'tarjetasDemo'   => config('app.debug')
                ? Tarjeta::activas()->limit(3)->pluck('codigo_rfid')
                : collect(),
        ]);
    }
 
    /**
     * Consulta AJAX al escanear una tarjeta.
     * Devuelve el titular, su saldo y su vehiculo, para que el
     * JS filtre la grilla por el tipo correcto.
     */
    public function buscarRfid(Request $request)
    {
        $codigo = trim($request->query('codigo', ''));
 
        // El Global Scope limita la busqueda a este parqueo: una
        // tarjeta de Economia no sirve en Arquitectura.
        $tarjeta = Tarjeta::with('usuario.vehiculos')
                          ->porRfid($codigo)
                          ->first();
 
        if (! $tarjeta) {
            return response()->json(['message' => 'Tarjeta no registrada en este parqueo.'], 404);
        }
 
        if (! $tarjeta->estaActiva()) {
            return response()->json(['message' => 'La tarjeta esta bloqueada.'], 422);
        }
 
        $vehiculo = $tarjeta->usuario->vehiculos()->where('activo', 1)->first();
 
        if (! $vehiculo) {
            return response()->json(['message' => 'El usuario no tiene vehiculo registrado.'], 422);
        }
 
        $parqueo = auth()->user()->parqueoAsignado;
 
        return response()->json([
            'tarjeta_id' => $tarjeta->id,
            'nombre'     => $tarjeta->usuario->nombre_completo,
            'ci'         => $tarjeta->usuario->ci ?? '--------',
            'saldo'      => number_format($tarjeta->saldo, 2),
            'tarifa'     => $parqueo->tarifaPara($vehiculo->tipo),
            'vehiculo'   => [
                'id'    => $vehiculo->id,
                'placa' => $vehiculo->placa,
                'tipo'  => $vehiculo->tipo,
            ],
        ]);
    }
 
    /**
     * Registra el ingreso. Un solo punto de entrada para los
     * dos modos: `tipo_ingreso` decide cual se aplica.
     *
     * El calculo del cobro sera identico en ambos casos; lo
     * unico que cambia es de donde saldra el dinero al salir.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_ingreso' => ['required', 'in:rfid,visitante'],
            'espacio_id'   => ['required', 'exists:espacios,id'],
        ], [
            'espacio_id.required' => 'Seleccione un espacio antes de confirmar.',
        ]);
 
        // El formulario se envia por fetch, asi que todas las
        // respuestas de este flujo son JSON.
 
        return $request->tipo_ingreso === 'rfid'
            ? $this->ingresoConTarjeta($request)
            : $this->ingresoVisitante($request);
    }
 
    // ----------------------------------------------------
    // Modo A: cliente con tarjeta RFID
    // ----------------------------------------------------
    private function ingresoConTarjeta(Request $request)
    {
        $request->validate([
            'codigo_rfid' => ['required', 'string', 'max:32'],
        ], [
            'codigo_rfid.required' => 'Escanee una tarjeta.',
        ]);
 
        // El Global Scope limita la busqueda a este parqueo: una
        // tarjeta de Economia no sirve en Arquitectura.
        $tarjeta = Tarjeta::with('usuario.vehiculos')
                          ->porRfid($request->codigo_rfid)
                          ->first();
 
        if (! $tarjeta) {
            return $this->error('Tarjeta no registrada en este parqueo.');
        }
 
        if (! $tarjeta->estaActiva()) {
            return $this->error('La tarjeta esta bloqueada.');
        }
 
        $vehiculo = $tarjeta->usuario->vehiculos()->where('activo', 1)->first();
 
        if (! $vehiculo) {
            return $this->error('El usuario no tiene vehiculo registrado.');
        }
 
        $espacio = Espacio::findOrFail($request->espacio_id);
 
        if ($error = $this->validarEspacio($espacio, $vehiculo->tipo)) {
            return $this->error($error);
        }
 
        if ($this->placaEstaDentro($vehiculo->placa)) {
            return $this->error("El vehiculo {$vehiculo->placa} ya esta dentro.");
        }
 
        // Avisa si no le alcanza para un periodo, pero deja
        // entrar: podra recargar antes de salir.
        $tarifa = $tarjeta->parqueo->tarifaPara($vehiculo->tipo);
        $aviso  = $tarjeta->saldo < $tarifa
            ? " Atencion: saldo de {$tarjeta->saldo} Bs, insuficiente para un periodo."
            : '';
 
        DB::transaction(function () use ($tarjeta, $vehiculo, $espacio) {
            RegistroIngreso::create([
                'tarjeta_id'   => $tarjeta->id,
                'vehiculo_id'  => $vehiculo->id,
                'espacio_id'   => $espacio->id,
                'hora_ingreso' => now(),
                'estado'       => 'activo',
            ]);
 
            $espacio->ocupar();
        });
 
        return response()->json([
            'success'        => true,
            'espacio_numero' => $espacio->numero,
            'message'        => "Ingreso registrado: {$vehiculo->placa}.{$aviso}",
        ]);
    }
 
    // ----------------------------------------------------
    // Modo B: visitante sin tarjeta, pagara en efectivo
    // ----------------------------------------------------
    private function ingresoVisitante(Request $request)
    {
        $datos = $request->validate([
            'placa'         => ['required', 'string', 'max:15'],
            'tipo_vehiculo' => ['required', 'in:moto,auto'],
        ], [
            'placa.required' => 'Ingrese la placa del vehiculo.',
        ]);
 
        $placa   = strtoupper(trim($datos['placa']));
        $espacio = Espacio::findOrFail($request->espacio_id);
 
        if ($error = $this->validarEspacio($espacio, $datos['tipo_vehiculo'])) {
            return $this->error($error);
        }
 
        if ($this->placaEstaDentro($placa)) {
            return $this->error("El vehiculo {$placa} ya esta dentro.");
        }
 
        DB::transaction(function () use ($placa, $datos, $espacio) {
            RegistroIngreso::create([
                'placa_visitante' => $placa,
                'tipo_visitante'  => $datos['tipo_vehiculo'],
                'espacio_id'      => $espacio->id,
                'hora_ingreso'    => now(),
                'estado'          => 'activo',
            ]);
 
            $espacio->ocupar();
        });
 
        return response()->json([
            'success'        => true,
            'espacio_numero' => $espacio->numero,
            'message'        => "Ingreso manual registrado: {$placa}. Cobro en efectivo al salir.",
        ]);
    }
 
    // ----------------------------------------------------
    // Salidas
    // ----------------------------------------------------
 
    /**
     * Listado de vehiculos dentro con su cobro estimado en
     * vivo, para que el encargado no cobre de menos.
     */
    public function salidas()
    {
        $activos = RegistroIngreso::activos()
            ->with(['vehiculo', 'espacio', 'tarjeta.usuario'])
            ->orderBy('hora_ingreso')
            ->get();
 
        $activos->each(fn ($r) => $r->estimado = $r->cobroEstimado());
 
        return view('salidas.index', [
            'activos' => $activos,
            'parqueo' => auth()->user()->parqueoAsignado,
        ]);
    }

    /** Datos calculados que consume el modal antes de registrar la salida. */
    public function previewSalida(RegistroIngreso $registro)
    {
        abort_unless($registro->estaActivo(), 422, 'Este registro ya fue cerrado.');

        $registro->loadMissing(['vehiculo', 'espacio', 'tarjeta.usuario']);
        $cobro = $registro->cobroEstimado();
        $tarjeta = $registro->tarjeta;

        return response()->json([
            'registro_id'      => $registro->id,
            'placa'            => $registro->placa,
            'titular'          => $registro->esVisitante() ? 'Visitante' : $tarjeta->usuario->nombre_completo,
            'espacio'          => $registro->espacio?->numero ?? '--',
            'tipo'             => $registro->tipo_vehiculo,
            'es_visitante'     => $registro->esVisitante(),
            'modo'             => $registro->metodo_pago,
            'hora_entrada'     => $registro->hora_ingreso->format('h:i A'),
            'hora_salida'      => now()->format('h:i A'),
            'tiempo_total'     => $registro->duracion,
            'periodos'         => $cobro['periodos'],
            'tarifa_unitaria'  => number_format($registro->parqueo->tarifaPara($registro->tipo_vehiculo), 2, '.', ''),
            'monto_total'      => number_format($cobro['monto'], 2, '.', ''),
            'saldo_disponible' => $tarjeta ? number_format($tarjeta->saldo, 2, '.', '') : '0.00',
            'tarjeta_id'       => $tarjeta?->id,
            'codigo_rfid'      => $tarjeta?->codigo_rfid,
        ]);
    }
 
    /**
     * Cobra, cierra el registro y libera el espacio.
     * El calculo es el mismo para clientes y visitantes.
     */
    public function salida(Request $request, RegistroIngreso $registro)
    {
        if (! $registro->estaActivo()) {
            return back()->withErrors('Este registro ya fue cerrado.');
        }
 
        $datos = $request->validate([
            'metodo_pago' => ['required', 'in:efectivo,tarjeta'],
            'codigo_rfid' => ['nullable', 'string', 'max:32'],
        ]);

        $salida      = now();
        $cobro       = $registro->cobroEstimado();
        $esVisitante = $registro->esVisitante();
        $tarjeta     = $registro->tarjeta;
 
        $usarEfectivo = $esVisitante || $datos['metodo_pago'] === 'efectivo';

        if (! $esVisitante && $usarEfectivo && $tarjeta->saldo >= $cobro['monto']) {
            return response()->json(['message' => 'Esta salida debe autorizarse con la tarjeta RFID.'], 422);
        }

        if (! $esVisitante && ! $usarEfectivo && $datos['codigo_rfid'] !== $tarjeta->codigo_rfid) {
            return response()->json(['message' => 'La tarjeta leída no corresponde al titular.'], 422);
        }

        if (! $usarEfectivo && $tarjeta->saldo < $cobro['monto']) {
            return back()->withErrors(
                "Saldo insuficiente: debe {$cobro['monto']} Bs y tiene {$tarjeta->saldo} Bs. " .
                'Recargue la tarjeta antes de registrar la salida.'
            );
        }
 
        DB::transaction(function () use ($registro, $tarjeta, $cobro, $salida, $usarEfectivo) {
 
            $pago = [
                'registro_id' => $registro->id,
                'monto'       => $cobro['monto'],
                'fecha_pago'  => $salida,
                'estado'      => 'exitoso',
            ];
 
            if ($usarEfectivo) {
                // Efectivo: no hay saldo que mover.
                $pago['metodo'] = 'efectivo';
            } else {
                $anterior = $tarjeta->saldo;
                $tarjeta->decrement('saldo', $cobro['monto']);
 
                $pago += [
                    'metodo'          => 'tarjeta',
                    'tarjeta_id'      => $tarjeta->id,
                    'saldo_anterior'  => $anterior,
                    'saldo_posterior' => $anterior - $cobro['monto'],
                ];
            }
 
            Pago::create($pago);
 
            $registro->update([
                'hora_salida'     => $salida,
                'periodos_usados' => $cobro['periodos'],
                'estado'          => 'finalizado',
            ]);
 
            $registro->espacio?->liberar();
        });
 
        $metodo = $usarEfectivo ? 'en efectivo' : 'de la tarjeta';

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with(
            'exito',
            "Salida de {$registro->placa}: {$cobro['periodos']} periodo(s), " .
            "{$cobro['monto']} Bs cobrados {$metodo}."
        );
    }
 
    // ----------------------------------------------------
    // Validaciones compartidas por los dos modos
    // ----------------------------------------------------
 
    /**
     * Respuesta de error para el flujo AJAX del formulario.
     */
    private function error(string $mensaje)
    {
        return response()->json(['success' => false, 'message' => $mensaje], 422);
    }
 
    /**
     * Un espacio de moto no admite autos y viceversa. Sin esto
     * una moto ocuparia un lugar de auto que vale 4 veces mas.
     */
    private function validarEspacio(Espacio $espacio, string $tipo): ?string
    {
        if (! $espacio->estaLibre()) {
            return "El espacio {$espacio->numero} ya esta ocupado.";
        }
 
        if ($espacio->tipo !== $tipo) {
            return "El espacio {$espacio->numero} es para {$espacio->tipo}, no para {$tipo}.";
        }
 
        return null;
    }
 
    /**
     * Busca la placa entre los ingresos activos, venga de un
     * vehiculo registrado o de un ingreso manual.
     */
    private function placaEstaDentro(string $placa): bool
    {
        return RegistroIngreso::activos()
            ->where(function ($q) use ($placa) {
                $q->where('placa_visitante', $placa)
                  ->orWhereHas('vehiculo', fn ($v) => $v->where('placa', $placa));
            })
            ->exists();
    }
}
