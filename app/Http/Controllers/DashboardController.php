<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Espacio;
use App\Models\Pago;
use App\Models\RegistroIngreso;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
 
        // El dashboard es por-parqueo: el usuario autenticado debe ser encargado de uno.
        // Si tu Global Scope (PerteneceAParqueo) ya resuelve esto automáticamente,
        // este bloque queda como validación de que el usuario sí tiene un parqueo asignado.
        $encargado = $usuario->encargado;
 
        if (! $encargado) {
            abort(403, 'Tu usuario no está asignado como encargado de ningún parqueo.');
        }
 
        $parqueoId = $encargado->parqueo_id;
 
        // --- Tarjetas KPI ---
        $espaciosLibres   = Espacio::where('parqueo_id', $parqueoId)->where('estado', 'libre')->count();
        $espaciosOcupados = Espacio::where('parqueo_id', $parqueoId)->where('estado', 'ocupado')->count();
        $capacidadTotal   = $espaciosLibres + $espaciosOcupados;
 
        $ingresosHoy = Pago::where('parqueo_id', $parqueoId)
            ->where('estado', 'exitoso')
            ->whereDate('fecha_pago', today())
            ->sum('monto');
 
        $vehiculosHoy = RegistroIngreso::where('parqueo_id', $parqueoId)
            ->whereDate('hora_ingreso', today())
            ->count();
 
        $ocupacionPorcentaje = $capacidadTotal > 0
            ? round(($espaciosOcupados / $capacidadTotal) * 100)
            : 0;
 
        // --- Últimos movimientos del día ---
        $movimientos = RegistroIngreso::with(['tarjeta.usuario', 'vehiculo', 'pago'])
            ->where('parqueo_id', $parqueoId)
            ->whereDate('hora_ingreso', today())
            ->latest('hora_ingreso')
            ->take(10)
            ->get();
 
        return view('dashboard.index', compact(
            'espaciosLibres',
            'espaciosOcupados',
            'ingresosHoy',
            'vehiculosHoy',
            'ocupacionPorcentaje',
            'movimientos',
        ));
    }
}
