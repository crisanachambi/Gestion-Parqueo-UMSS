<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

/**
 * Transaccion de cobro. Entidad:
 * monto, fecha, metodo y estado de cada operacion.
 */

class Pago extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;

    protected $table = 'pagos';
    protected $guarded = ['id'];
 
    protected $casts = [
        'monto'           => 'decimal:2',
        'saldo_anterior'  => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
        'fecha_pago'      => 'datetime',
    ];
    

    // ---------------- Relaciones ----------------
 
    public function registro()
    {
        return $this->belongsTo(RegistroIngreso::class, 'registro_id');
    }
 
    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class);
    }
 
    public function parqueo()
    {
        return $this->belongsTo(Parqueo::class);
    }
 
    // ---------------- Scopes ----------------
 
    public function scopeExitosos($query)
    {
        return $query->where('estado', 'exitoso');
    }
 
    public function scopeDeHoy($query)
    {
        return $query->whereDate('fecha_pago', today());
    }
 
    public function scopeDelMes($query, ?int $mes = null, ?int $anio = null)
    {
        return $query->whereMonth('fecha_pago', $mes ?? now()->month)
                     ->whereYear('fecha_pago', $anio ?? now()->year);
    }
 
    /** Cobros descontados de saldo */
    public function scopePorTarjeta($query)
    {
        return $query->where('metodo', 'tarjeta');
    }
 
    /**
     * Cobros en efectivo (visitantes sin tarjeta).
     * Este dinero NO pasa por el sistema: queda en la caja
     * del encargado, por eso se reporta por separado.
     */
    public function scopeEnEfectivo($query)
    {
        return $query->where('metodo', 'efectivo');
    }
 
    // ---------------- Reportes ----------------
 
    /**
     * Reparto de ingresos: 70% encargado / 30% facultad.
     * Reemplaza a la tabla `reportes` que se elimino.
     */
    public static function distribucion($query = null): array
    {
        $total = (float) ($query ?? static::exitosos())->sum('monto');
 
        return [
            'total'     => round($total, 2),
            'encargado' => round($total * 0.70, 2),
            'facultad'  => round($total * 0.30, 2),
        ];
    }
}
