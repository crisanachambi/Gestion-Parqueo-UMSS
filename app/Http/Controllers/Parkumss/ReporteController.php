<?php

namespace App\Http\Controllers\Parkumss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Recarga;
use App\Models\RegistroIngreso;
use Illuminate\Support\Facades\DB;

/**
 * Reportes del parqueo.
 * Reemplazan a la tabla `reportes` que se elimino del diseño: todo se calcula con consultas agregadas sobre pagos y
 * registros_ingreso, asi los totales siempre reflejan el
 * estado real y no se desincronizan.
 */

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->date('desde') ?? now()->startOfMonth();
        $hasta = $request->date('hasta') ?? now()->endOfDay();
 
        $pagos = Pago::exitosos()->whereBetween('fecha_pago', [$desde, $hasta]);
 
        $porTarjeta  = (float) (clone $pagos)->porTarjeta()->sum('monto');
        $enEfectivo  = (float) (clone $pagos)->enEfectivo()->sum('monto');
        $total       = $porTarjeta + $enEfectivo;
        $cantidad    = (clone $pagos)->count();
 
        $ingresos = RegistroIngreso::whereBetween('hora_ingreso', [$desde, $hasta]);
 
        return view('reportes.index', [
            'desde'   => $desde,
            'hasta'   => $hasta,
            'parqueo' => auth()->user()->parqueoAsignado,
 
            // --- Totales del periodo ---
            'total'       => $total,
            'porTarjeta'  => $porTarjeta,
            'enEfectivo'  => $enEfectivo,
            'cantidad'    => $cantidad,
            'promedio'    => $cantidad ? round($total / $cantidad, 2) : 0,
 
            // --- Reparto acordado con la facultad ---
            'encargado70' => round($total * 0.70, 2),
            'facultad30'  => round($total * 0.30, 2),
 
            // --- Volumen de uso ---
            'ingresosTotal'      => (clone $ingresos)->count(),
            'ingresosConTarjeta' => (clone $ingresos)->conTarjeta()->count(),
            'ingresosVisitantes' => (clone $ingresos)->visitantes()->count(),
 
            // --- Recargas del periodo ---
            'recargasMonto' => (float) Recarga::exitosas()
                ->whereBetween('fecha_recarga', [$desde, $hasta])->sum('monto'),
 
            // --- Series y detalle ---
            'porDia'   => $this->recaudacionPorDia($desde, $hasta),
            'detalle'  => $this->detalle($desde, $hasta),
        ]);
    }
 
    /**
     * Recaudacion diaria, para la grafica.
     */
    private function recaudacionPorDia($desde, $hasta)
    {
        return Pago::exitosos()
            ->whereBetween('fecha_pago', [$desde, $hasta])
            ->selectRaw('DATE(fecha_pago) AS dia')
            ->selectRaw("SUM(CASE WHEN metodo = 'tarjeta'  THEN monto ELSE 0 END) AS tarjeta")
            ->selectRaw("SUM(CASE WHEN metodo = 'efectivo' THEN monto ELSE 0 END) AS efectivo")
            ->selectRaw('SUM(monto) AS total, COUNT(*) AS cobros')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();
    }
 
    /**
     * Detalle de cada cobro, para la tabla exportable.
     */
    private function detalle($desde, $hasta)
    {
        return Pago::exitosos()
            ->with('registro.vehiculo', 'registro.espacio', 'tarjeta.usuario')
            ->whereBetween('fecha_pago', [$desde, $hasta])
            ->orderByDesc('fecha_pago')
            ->paginate(50)
            ->withQueryString();
    }
}
