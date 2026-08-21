<?php

namespace App\Http\Controllers\Parkumss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarjeta;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Gestion de clientes y emision de tarjetas.
 *
 * CLIENTE COMPARTIDO: `usuarios` y `vehiculos` NO llevan Global
 * Scope, asi que una persona registrada en Economia aparece al
 * buscarla desde Arquitectura. Lo privado es la tarjeta y su
 * saldo, que si estan aislados por parqueo.
 *
 * Aqui NO se recarga saldo: el monto solo aparece al emitir la
 * tarjeta. Para recargar se enlaza al modulo Recargas, que es
 * el unico punto del sistema que lo hace.
 */

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        // Clientes de ESTE parqueo: los que tienen tarjeta aqui.
        $usuarios = Usuario::clientes()
            ->whereHas('tarjetas', fn ($q) => $q->where(
                'parqueo_id', auth()->user()->parqueo_asignado_id
            ))
            ->with('tarjetaActual')
            ->buscar($request->q)
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();
 
        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'q'        => $request->q,
            'parqueo'  => auth()->user()->parqueoAsignado,
        ]);
    }

    /** Muestra la ficha encontrada desde el buscador por C.I. */
    public function buscar(Request $request)
    {
        $datos = $request->validate(['ci' => ['required', 'string', 'max:20']]);

        $usuario = Usuario::clientes()
            ->with(['vehiculos', 'tarjetaActual'])
            ->where('ci', trim($datos['ci']))
            ->first();

        if (! $usuario) {
            return redirect()->route('usuarios.index')
                ->with('error_no_encontrado', 'No se encontró un usuario con ese C.I.');
        }

        return view('usuarios.index', [
            'usuario' => $usuario,
            'tarjeta' => $usuario->tarjetaActual,
            'parqueo' => auth()->user()->parqueoAsignado,
        ]);
    }

    /** Obtiene la última tarjeta no registrada leída por el hardware de este parqueo */
    public function ultimaTarjetaEscaneada()
    {
        $parqueoId = auth()->user()->parqueo_asignado_id;
        // Obtenemos y borramos de caché para no leerla dos veces
        $codigo = \Illuminate\Support\Facades\Cache::pull('ultima_tarjeta_escaneada_' . $parqueoId);
        
        return response()->json(['codigo' => $codigo]);
    }

    /**
     * Busqueda por CI. Devuelve uno de tres estados, que son
     * las tres variantes que dibuja la vista.
     */
    public function buscarPorCi(Request $request)
    {
        $request->validate(['ci' => ['required', 'string', 'max:20']]);
 
        $usuario = Usuario::clientes()
                          ->with('vehiculos')
                          ->where('ci', trim($request->ci))
                          ->first();
 
        // Estado 1: no existe en ningun parqueo.
        if (! $usuario) {
            return response()->json(['estado' => 'no_existe', 'ci' => $request->ci]);
        }
 
        $tarjeta = Tarjeta::where('usuario_id', $usuario->id)->first();
 
        $datos = [
            'usuario'   => $usuario->only(['id', 'nombre', 'apellido', 'ci', 'telefono', 'categoria']),
            'vehiculos' => $usuario->vehiculos->map->only(['id', 'placa', 'tipo', 'marca', 'color']),
            // Solo el parqueo que lo registro puede editarlo.
            'editable'  => $usuario->parqueo_origen_id === auth()->user()->parqueo_asignado_id,
            'origen'    => $usuario->parqueoOrigen?->nombre,
        ];
 
        // Estado 2: existe pero sin tarjeta aqui.
        if (! $tarjeta) {
            return response()->json($datos + ['estado' => 'sin_tarjeta']);
        }
 
        // Estado 3: ya es cliente de este parqueo.
        return response()->json($datos + [
            'estado'  => 'cliente',
            'tarjeta' => [
                'id'     => $tarjeta->id,
                'codigo' => $tarjeta->codigo_rfid,
                'saldo'  => (float) $tarjeta->saldo,
                'estado' => $tarjeta->estado,
            ],
        ]);
    }
 
    /**
     * Alta completa: persona, vehiculos y tarjeta opcional.
     *
     * La tarjeta puede omitirse porque hay quien solo quiere la
     * app movil para ver disponibilidad y no compra plastico.
     */
    public function store(Request $request)
    {
        $parqueoId = auth()->user()->parqueo_asignado_id;
 
        $datos = $request->validate([
            // Si viene usuario_id, la persona ya existe y solo
            // se le emite la tarjeta de este parqueo.
            'usuario_id' => ['nullable', 'exists:usuarios,id'],
 
            'nombre'    => ['required_without:usuario_id', 'string', 'max:100'],
            'apellido'  => ['nullable', 'string', 'max:100'],
            'ci'        => [
                'required_without:usuario_id', 'string', 'max:20',
                Rule::unique('usuarios')->whereNull('deleted_at')
                    ->ignore($request->usuario_id),
            ],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'categoria' => ['nullable', 'in:estudiante,docente,administrativo,visitante'],
 
            'vehiculos'           => ['nullable', 'array'],
            'vehiculos.*.placa'   => ['required', 'string', 'max:15'],
            'vehiculos.*.tipo'    => ['required', 'in:moto,auto'],
            'vehiculos.*.marca'   => ['nullable', 'string', 'max:60'],
            'vehiculos.*.color'   => ['nullable', 'string', 'max:40'],
 
            'sin_tarjeta'   => ['nullable', 'boolean'],
            'codigo_rfid'   => ['required_if:sin_tarjeta,0,null', 'nullable', 'string', 'max:32'],
            'saldo_inicial' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ], [
            'ci.required_without'          => 'El carnet es obligatorio: es lo que permite encontrar a la persona despues.',
            'ci.unique'                    => 'Ya existe una persona registrada con ese carnet.',
            'codigo_rfid.required_if'      => 'Escanee la tarjeta o marque "registrar sin tarjeta".',
        ]);
 
        $resultado = DB::transaction(function () use ($datos, $parqueoId, $request) {
 
            $usuario = isset($datos['usuario_id'])
                ? Usuario::findOrFail($datos['usuario_id'])
                : Usuario::create([
                    'nombre'            => $datos['nombre'],
                    'apellido'          => $datos['apellido'] ?? null,
                    'ci'                => $datos['ci'],
                    'telefono'          => $datos['telefono'] ?? null,
                    'categoria'         => $datos['categoria'] ?? null,
                    'rol'               => 'usuario',
                    'parqueo_origen_id' => $parqueoId,
                ]);
 
            foreach ($datos['vehiculos'] ?? [] as $v) {
                // firstOrCreate: si la placa ya existe (otro
                // parqueo lo registro) no la duplica.
                Vehiculo::firstOrCreate(
                    ['placa' => strtoupper(trim($v['placa']))],
                    [
                        'usuario_id' => $usuario->id,
                        'tipo'       => $v['tipo'],
                        'marca'      => $v['marca'] ?? null,
                        'color'      => $v['color'] ?? null,
                    ]
                );
            }
 
            $tarjeta = null;
 
            if (! $request->boolean('sin_tarjeta')) {
                // El UNIQUE (usuario_id, parqueo_id, vigente)
                // impide dos tarjetas suyas en este parqueo.
                $tarjeta = Tarjeta::create([
                    'usuario_id'  => $usuario->id,
                    'codigo_rfid' => $datos['codigo_rfid'],
                    'saldo'       => $datos['saldo_inicial'] ?? 0,
                    'estado'      => 'activa',
                ]);
            }
 
            return compact('usuario', 'tarjeta');
        });
 
        $mensaje = $resultado['tarjeta']
            ? "Tarjeta {$resultado['tarjeta']->codigo_rfid} emitida a {$resultado['usuario']->nombre_completo}."
            : "{$resultado['usuario']->nombre_completo} registrado sin tarjeta.";
 
        return redirect()->route('usuarios.index')->with('exito', $mensaje);
    }
 
    /**
     * Edicion manual de saldo (aumentar o disminuir directamente).
     */
    public function editarSaldo(Request $request, Tarjeta $tarjeta)
    {
        $request->validate([
            'nuevo_saldo' => ['required', 'numeric', 'min:0', 'max:5000'],
        ]);

        $nuevo_total = $tarjeta->saldo + $request->nuevo_saldo;

        $tarjeta->update([
            'saldo' => $nuevo_total,
        ]);

        return back()->with('exito', "Se recargaron Bs. {$request->nuevo_saldo}. El nuevo saldo es Bs. {$nuevo_total}.");
    }

    /**
     * Edicion de datos personales. Solo puede hacerlo el
     * parqueo que registro a la persona, para que dos
     * encargados no se pisen la informacion.
     */
    public function update(Request $request, Usuario $usuario)
    {
        if ($usuario->parqueo_origen_id !== auth()->user()->parqueo_asignado_id) {
            return back()->withErrors(
                'Esta persona fue registrada por otro parqueo. Solo puede consultarla.'
            );
        }
 
        $datos = $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'apellido'  => ['nullable', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'categoria' => ['nullable', 'in:estudiante,docente,administrativo,visitante'],
            'vehiculos'           => ['nullable', 'array'],
            'vehiculos.*.placa'   => ['nullable', 'string', 'max:15'],
            'vehiculos.*.tipo'    => ['nullable', 'in:moto,auto'],
            'vehiculos.*.marca'   => ['nullable', 'string', 'max:60'],
            'vehiculos.*.color'   => ['nullable', 'string', 'max:40'],
            'codigo_rfid'         => ['nullable', 'string', 'max:32'],
        ]);
 
        $usuario->update([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'categoria' => $datos['categoria'] ?? null,
        ]);

        // Actualizar vehículo principal si se proporciona la placa
        if (!empty($datos['vehiculos'][0]['placa'])) {
            $v = $datos['vehiculos'][0];
            $vehiculo = $usuario->vehiculos()->first();
            
            if ($vehiculo) {
                $vehiculo->update([
                    'placa' => strtoupper(trim($v['placa'])),
                    'tipo' => $v['tipo'] ?? 'auto',
                    'marca' => $v['marca'] ?? null,
                    'color' => $v['color'] ?? null,
                ]);
            } else {
                Vehiculo::create([
                    'usuario_id' => $usuario->id,
                    'placa' => strtoupper(trim($v['placa'])),
                    'tipo' => $v['tipo'] ?? 'auto',
                    'marca' => $v['marca'] ?? null,
                    'color' => $v['color'] ?? null,
                ]);
            }
        }

        // Actualizar tarjeta RFID si se proporciona
        if (!empty($datos['codigo_rfid'])) {
            $tarjeta = $usuario->tarjetaActual;
            
            if ($tarjeta) {
                if ($tarjeta->codigo_rfid !== $datos['codigo_rfid']) {
                    // Si cambia la tarjeta, actualiza el código y reinicia el saldo a 0
                    $tarjeta->update([
                        'codigo_rfid' => $datos['codigo_rfid'],
                        'saldo' => 0
                    ]);
                }
            } else {
                // Si el usuario no tenía tarjeta activa, se le asigna una nueva
                Tarjeta::create([
                    'usuario_id' => $usuario->id,
                    'codigo_rfid' => $datos['codigo_rfid'],
                    'saldo' => 0,
                    'estado' => 'activa',
                ]);
            }
        }
 
        return back()->with('exito', 'Datos actualizados.');
    }
 
    /**
     * Bloquea la tarjeta. El codigo RFID queda liberado por la
     * columna `vigente`, asi que puede reusarse en otra nueva.
     */
    public function bloquear(Tarjeta $tarjeta)
    {
        $tarjeta->update(['estado' => 'bloqueada']);
 
        return back()->with('exito', "Tarjeta {$tarjeta->codigo_rfid} bloqueada.");
    }
 
    public function desbloquear(Tarjeta $tarjeta)
    {
        $tarjeta->update(['estado' => 'activa']);
 
        return back()->with('exito', "Tarjeta {$tarjeta->codigo_rfid} reactivada.");
    }
}
