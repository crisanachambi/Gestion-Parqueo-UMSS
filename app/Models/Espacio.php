<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

class Espacio extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;
 
    protected $table = 'espacios';
    protected $guarded = ['id', 'vigente'];
 
    // ---------------- Relaciones ----------------
 
    public function parqueo()
    {
        return $this->belongsTo(Parqueo::class);
    }
 
    public function registros()
    {
        return $this->hasMany(RegistroIngreso::class);
    }
 
    /** El registro que lo esta ocupando ahora mismo */
    public function registroActivo()
    {
        return $this->hasOne(RegistroIngreso::class)->where('estado', 'activo');
    }
 
    // ---------------- Accesores para las vistas ----------------
 
    /** Alias de `numero`: la vista del mapa usa `codigo` */
    public function getCodigoAttribute(): string
    {
        return $this->numero;
    }
 
    /** Placa del vehiculo que lo ocupa, o cadena vacia */
    public function getPlacaAttribute(): string
    {
        return $this->registroActivo?->placa ?? '';
    }
 
    // ---------------- Helpers ----------------
 
    public function estaLibre(): bool
    {
        return $this->estado === 'libre';
    }
 
    /** Un espacio de moto no admite autos y viceversa */
    public function admite(Vehiculo $vehiculo): bool
    {
        return $this->tipo === $vehiculo->tipo;
    }
 
    public function ocupar(): void
    {
        $this->update(['estado' => 'ocupado']);
    }
 
    public function liberar(): void
    {
        $this->update(['estado' => 'libre']);
    }
 
    // ---------------- Scopes ----------------
 
    public function scopeLibres($query)
    {
        return $query->where('estado', 'libre');
    }
 
    public function scopeParaTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
