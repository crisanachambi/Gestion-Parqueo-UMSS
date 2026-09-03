<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;

class Vehiculo extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'vehiculos';
    protected $guarded = ['id', 'vigente'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ---------------- Relaciones ----------------
 
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
 
    public function registros()
    {
        return $this->hasMany(RegistroIngreso::class);
    }
 
    // ---------------- Helpers ----------------
 
    public function esMoto(): bool
    {
        return $this->tipo === 'moto';
    }
 
    /** Registro abierto, si el vehiculo esta dentro ahora */
    public function ingresoActivo(): ?RegistroIngreso
    {
        return $this->registros()->where('estado', 'activo')->first();
    }
 
    public function estaDentro(): bool
    {
        return $this->registros()->where('estado', 'activo')->exists();
    }
 
    // ---------------- Mutadores ----------------
 
    /** La placa siempre en mayusculas y sin espacios */
    public function setPlacaAttribute($valor): void
    {
        $this->attributes['placa'] = strtoupper(trim($valor));
    }
}
