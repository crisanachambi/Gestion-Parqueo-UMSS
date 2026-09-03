<?php

namespace App\Http\Controllers\Parkumss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recarga;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\DB;

/**
 * Carga de saldo a las tarjetas.
 *
 * Es el UNICO punto del sistema donde se recarga. La ficha del
 * usuario solo enlaza aqui con la tarjeta preseleccionada, para
 * no duplicar el formulario en dos modulos.
 */

class RecargaController extends Controller
{
    public function index(Request $request)
    {
        // Si llega desde la ficha de un usuario, la tarjeta ya
        // viene elegida y se omite el escaneo.
        $tarjeta = $request->filled('tarjeta')
            ? Tarjeta::with('usuario')->find($request->tarjeta)
            : null;
 
        $delDia = Recarga::exitosas()
            ->deHoy()
            ->with('tarjeta.usuario', 'encargado')
            ->orderByDesc('fecha_recarga')
            ->get();
 
        return view('recargas.index', [
            'tarjeta'      => $tarjeta,
            'recargasHoy'  => $delDia,
            'totalHoy'     => (float) $delDia->sum('monto'),
            'parqueo'      => auth()->user()->parqueoAsignado,
        ]);
    }
 
    /**
     * Consulta AJAX al escanear una tarjeta.
     * Devuelve titular y saldo para la ficha compacta.
     */
    public function buscarRfid(Request $request)
    {
        $tarjeta = Tarjeta::with('usuario')
                          ->porRfid(trim($request->query('codigo', '')))
                          ->first();
 
        if (! $tarjeta) {
            return response()->json(['message' => 'Tarjeta no registrada en este parqueo.'], 404);
        }
 
        return response()->json([
            'tarjeta_id' => $tarjeta->id,
            'codigo'     => $tarjeta->codigo_rfid,
            'nombre'     => $tarjeta->usuario->nombre_completo,
            'ci'         => $tarjeta->usuario->ci ?? '--------',
            'saldo'      => (float) $tarjeta->saldo,
            'bloqueada'  => ! $tarjeta->estaActiva(),
        ]);
    }
 
    /**
     * Registra la recarga. Toca dos tablas (tarjetas y
     * recargas), asi que va dentro de una transaccion: si algo
     * falla, no queda saldo sumado sin su comprobante.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'tarjeta_id' => ['required', 'exists:tarjetas,id'],
            'monto'      => ['required', 'numeric', 'min:1', 'max:1000'],
        ], [
            'monto.min' => 'El monto minimo de recarga es 1 Bs.',
            'monto.max' => 'El monto maximo por recarga es 1000 Bs.',
        ]);
 
        // findOrFail pasa por el Global Scope: una tarjeta de
        // otro parqueo simplemente no existe para este encargado.
        $tarjeta = Tarjeta::findOrFail($datos['tarjeta_id']);
 
        if (! $tarjeta->estaActiva()) {
            return back()->withErrors('La tarjeta esta bloqueada. Desbloqueela antes de recargar.');
        }
 
        DB::transaction(function () use ($tarjeta, $datos) {
            $anterior = $tarjeta->saldo;
 
            $tarjeta->increment('saldo', $datos['monto']);
 
            Recarga::create([
                'tarjeta_id'      => $tarjeta->id,
                'encargado_id'    => auth()->id(),
                'monto'           => $datos['monto'],
                'saldo_anterior'  => $anterior,
                'saldo_posterior' => $anterior + $datos['monto'],
                'estado'          => 'exitosa',
                'fecha_recarga'   => now(),
            ]);
        });
 
        return back()->with(
            'exito',
            "Recarga de {$datos['monto']} Bs a {$tarjeta->usuario->nombre_completo}. " .
            "Nuevo saldo: {$tarjeta->fresh()->saldo} Bs."
        );
    }
}
