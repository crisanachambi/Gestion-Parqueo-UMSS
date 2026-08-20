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
        // Espacios con el registro activo que los ocupa, para
        // poder mostrar la placa dentro de cada casilla.
        $espacios = Espacio::with('registroActivo.vehiculo')
                           ->orderBy('numero')
                           ->get();
 
        $ocupados     = $espacios->where('estado', 'ocupado')->count();
        $disponibles  = $espacios->where('estado', 'libre')->count();
        $capacidad    = $espacios->count();
 
        $activos = RegistroIngreso::activos()
            ->with(['vehiculo.usuario', 'espacio', 'tarjeta.usuario'])
            ->orderByDesc('hora_ingreso')
            ->get();
 
        // Recaudacion del dia separada por metodo: el efectivo
        // no pasa por el sistema, queda en la caja del encargado.
        $recaudTarjeta  = (float) Pago::exitosos()->deHoy()->porTarjeta()->sum('monto');
        $recaudEfectivo = (float) Pago::exitosos()->deHoy()->enEfectivo()->sum('monto');
 
        $recargas = Recarga::exitosas()->deHoy();
 
        $movimientosRfid = RegistroIngreso::with(['vehiculo.usuario', 'espacio', 'tarjeta.usuario', 'pago'])
            ->whereNotNull('tarjeta_id')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view('dashboard.index', [
            // --- KPI: ocupacion ---
            'ocupadosCount'       => $ocupados,
            'disponiblesCount'    => $disponibles,
            'capacidadTotal'      => $capacidad,
            'porcentajeOcupacion' => $capacidad ? round($ocupados / $capacidad * 100) : 0,
 
            // --- KPI: ingresos activos ---
            'ingresosActivosCount' => $activos->count(),
            'ingresosTarjeta'      => $activos->whereNotNull('tarjeta_id')->count(),
            'ingresosVisitantes'   => $activos->whereNull('tarjeta_id')->count(),
 
            // --- KPI: recaudacion ---
            'recaudacionTotal'    => $recaudTarjeta + $recaudEfectivo,
            'recaudacionTarjeta'  => $recaudTarjeta,
            'recaudacionEfectivo' => $recaudEfectivo,
 
            // --- KPI: recargas ---
            'recargasCount'       => $recargas->count(),
            'montoRecargadoTotal' => (float) $recargas->sum('monto'),
 
            // --- Mapa de espacios ---
            'espaciosAutos' => $espacios->where('tipo', 'auto'),
            'espaciosMotos' => $espacios->where('tipo', 'moto'),
 
            // --- Lista lateral ---
            'vehiculosDentro' => $this->paraLista($activos),

            // --- Hardware RFID ---
            'movimientosRfid' => $movimientosRfid,
            'ultimoMovimiento' => $movimientosRfid->first(),
        ]);
    }


    /**
     * Adapta los registros al formato que espera la vista.
     * Sirve igual para clientes con tarjeta y para visitantes:
     * los accesores del modelo resuelven de donde sale la placa.
     */
    private function paraLista($activos)
    {
        return $activos->map(fn ($r) => (object) [
            'placa'               => $r->placa,
            'tipo'                => $r->tipo_vehiculo,
            'usuario_nombre'      => $r->tarjeta?->usuario?->nombre_completo ?? 'Visitante',
            'espacio_codigo'      => $r->espacio->numero ?? '--',
            'hora_ingreso'        => $r->hora_ingreso->format('H:i'),
            'tiempo_transcurrido' => $r->duracion,
            'metodo_pago'         => $r->esVisitante() ? 'Efectivo' : 'RFID',
            'cobro_estimado'      => $r->cobroEstimado(),
        ]);
    }
}
