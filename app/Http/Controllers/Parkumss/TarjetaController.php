<?php

namespace App\Http\Controllers\Parkumss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recarga;
use App\Models\Tarjeta;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class TarjetaController extends Controller
{
    /**
     * Buscar un cliente por CI antes de darle tarjeta.
     *
     * Aqui se ve el CLIENTE COMPARTIDO: la persona puede existir
     * porque otro parqueo ya la registro. En ese caso no se crea
     * de nuevo, solo se le emite la tarjeta de ESTE parqueo.
     */
    public function buscarPorCi(Request $request)
    {
        $request->validate(['ci' => ['required', 'string']]);
 
        // Usuario NO lleva Global Scope: se busca en los 3 parqueos.
        $usuario = Usuario::clientes()
                          ->with('vehiculos')
                          ->where('ci', $request->ci)
                          ->first();
 
        if (! $usuario) {
            return response()->json(['existe' => false]);
        }
 
        // Tarjeta SI lleva Global Scope: solo mira este parqueo.
        $tarjeta = Tarjeta::where('usuario_id', $usuario->id)->first();
 
        return response()->json([
            'existe'      => true,
            'usuario'     => $usuario->only(['id', 'nombre', 'apellido', 'ci', 'telefono', 'categoria']),
            'vehiculos'   => $usuario->vehiculos,
            'tiene_tarjeta' => (bool) $tarjeta,
            'saldo'       => $tarjeta?->saldo,
            // Solo puede editar sus datos el parqueo que lo registro.
            'puede_editar' => $usuario->parqueo_origen_id === auth()->user()->parqueo_asignado_id,
        ]);
    }

    /**
     * Emitir una tarjeta. Si el usuario no existe, lo crea.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'usuario_id'  => ['nullable', 'exists:usuarios,id'],
            'nombre'      => ['required_without:usuario_id', 'string', 'max:100'],
            'apellido'    => ['nullable', 'string', 'max:100'],
            'ci'          => ['required_without:usuario_id', 'string', 'max:20'],
            'telefono'    => ['nullable', 'string', 'max:20'],
            'categoria'   => ['nullable', 'in:estudiante,docente,administrativo,visitante'],
            'codigo_rfid' => ['required', 'string', 'max:32'],
            'saldo'       => ['nullable', 'numeric', 'min:0'],
        ]);
 
        $parqueoId = auth()->user()->parqueo_asignado_id;
 
        $tarjeta = DB::transaction(function () use ($datos, $parqueoId) {
 
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
 
            // El UNIQUE (usuario_id, parqueo_id, vigente) impide
            // dos tarjetas del mismo usuario en este parqueo.
            return Tarjeta::create([
                'usuario_id'  => $usuario->id,
                'codigo_rfid' => $datos['codigo_rfid'],
                'saldo'       => $datos['saldo'] ?? 0,
                'estado'      => 'activa',
            ]);
        });
 
        return back()->with('exito', "Tarjeta {$tarjeta->codigo_rfid} emitida.");
    }

    /**
     * Recargar saldo. Escribe en dos tablas, asi que va
     * dentro de una transaccion.
     */
    public function recargar(Request $request, Tarjeta $tarjeta)
    {
        $datos = $request->validate([
            'monto' => ['required', 'numeric', 'min:1', 'max:1000'],
        ]);
 
        if (! $tarjeta->estaActiva()) {
            return back()->withErrors('La tarjeta esta bloqueada.');
        }
 
        DB::transaction(function () use ($tarjeta, $datos) {
            $saldoAnterior = $tarjeta->saldo;
 
            $tarjeta->increment('saldo', $datos['monto']);
 
            Recarga::create([
                'tarjeta_id'      => $tarjeta->id,
                'encargado_id'    => auth()->id(),
                'monto'           => $datos['monto'],
                'saldo_anterior'  => $saldoAnterior,
                'saldo_posterior' => $saldoAnterior + $datos['monto'],
                'estado'          => 'exitosa',
                'fecha_recarga'   => now(),
            ]);
        });
 
        return back()->with('exito', "Recarga de {$datos['monto']} Bs registrada.");
    }

    /**
     * Bloquear una tarjeta perdida.
     * El codigo RFID queda liberado gracias a la columna
     * `vigente`, asi que se puede reusar en una tarjeta nueva.
     */
    public function bloquear(Tarjeta $tarjeta)
    {
        $tarjeta->update(['estado' => 'bloqueada']);
 
        return back()->with('exito', 'Tarjeta bloqueada.');
    }
}
