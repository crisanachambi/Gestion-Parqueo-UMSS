<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

/**
 * La estadia: entrada y salida. El cobro vive en Pago.
 */

class RegistroIngreso extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;

    protected $table = 'registros_ingreso';   // Laravel adivinaría 'registro_ingresos', por eso lo fijamos
    protected $guarded = ['id'];

    protected $casts = [
        'hora_ingreso'       => 'datetime',
        'hora_salida'        => 'datetime',
        'tiempo_permanencia' => 'integer',
    ];

    // ---------------- Relaciones ----------------
 
    public function parqueo()
    {
        return $this->belongsTo(Parqueo::class);
    }
 
    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class);
    }
 
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
 
    public function espacio()
    {
        return $this->belongsTo(Espacio::class);
    }
 
    public function pago()
    {
        return $this->hasOne(Pago::class, 'registro_id');
    }
 
    // ---------------- Helpers ----------------
 
    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }
 
    /** Visitante ocasional: sin tarjeta, paga en efectivo */
    public function esVisitante(): bool
    {
        return $this->tarjeta_id === null;
    }
 
    /**
     * La placa, venga del vehiculo registrado o del ingreso
     * manual. Evita tener que preguntar el tipo en las vistas.
     */
    public function getPlacaAttribute(): string
    {
        return $this->esVisitante()
            ? $this->placa_visitante
            : $this->vehiculo->placa;
    }
 
    /** El tipo (moto o auto), venga de donde venga */
    public function getTipoVehiculoAttribute(): string
    {
        return $this->esVisitante()
            ? $this->tipo_visitante
            : $this->vehiculo->tipo;
    }
 
    /** Como se cobra esta estadia */
    public function getMetodoPagoAttribute(): string
    {
        return $this->esVisitante() ? 'efectivo' : 'tarjeta';
    }
 
    /** Minutos transcurridos; si sigue dentro, hasta ahora */
    public function minutosTranscurridos(): int
    {
        return $this->hora_ingreso->diffInMinutes($this->hora_salida ?? now());
    }
 
    /** Duracion legible para las tablas: "2h 15min" */
    public function getDuracionAttribute(): string
    {
        $m = $this->minutosTranscurridos();
 
        return intdiv($m, 60) . 'h ' . ($m % 60) . 'min';
    }
 
    /**
     * Cuanto se cobraria si saliera ahora mismo.
     * Sirve igual para clientes y visitantes: el calculo del
     * cobro es identico, solo cambia de donde sale el dinero.
     */
    public function cobroEstimado(): array
    {
        return $this->parqueo->calcularCobro(
            $this->hora_ingreso,
            $this->hora_salida ?? now(),
            $this->tipo_vehiculo
        );
    }
 
    // ---------------- Scopes ----------------
 
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
 
    public function scopeDeHoy($query)
    {
        return $query->whereDate('hora_ingreso', today());
    }
 
    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('hora_ingreso', [$desde, $hasta]);
    }
 
    /** Ingresos manuales, sin tarjeta */
    public function scopeVisitantes($query)
    {
        return $query->whereNull('tarjeta_id');
    }
 
    /** Ingresos con tarjeta */
    public function scopeConTarjeta($query)
    {
        return $query->whereNotNull('tarjeta_id');
    }
}
