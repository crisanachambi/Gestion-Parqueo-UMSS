<?php

namespace App\Http\Controllers\Parkumss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Espacio;
use App\Models\Pago;
use App\Models\RegistroIngreso;
use App\Models\Recarga;

class DashboardController extends Controller
{
    public function index()
    {
        $espacios = Espacio::orderBy('numero')->get();
 
        $activos = RegistroIngreso::activos()
            ->with(['vehiculo', 'espacio'])
            ->orderBy('hora_ingreso')
            ->get();
 
        $activos->each(fn ($r) => $r->estimado = $r->cobroEstimado());
 
        return view('parkumss.dashboard.index', [
            'espacios'       => $espacios,
            'libres'         => $espacios->where('estado', 'libre')->count(),
            'ocupados'       => $espacios->where('estado', 'ocupado')->count(),
            'activos'        => $activos,
 
            // Recaudacion del dia, separada por metodo: el
            // efectivo no pasa por el sistema, queda en caja.
            'cobradoTarjeta' => Pago::exitosos()->deHoy()->porTarjeta()->sum('monto'),
            'cobradoEfectivo'=> Pago::exitosos()->deHoy()->enEfectivo()->sum('monto'),
            'recargado'      => Recarga::exitosas()->deHoy()->sum('monto'),
 
            'parqueo'        => auth()->user()->parqueoAsignado,
        ]);
    }
}
