<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Base obligatoria para autenticación
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;

/**
 * COMPARTIDO entre los 3 parqueos: sin PerteneceAParqueo.
 * La persona es unica en la universidad; su relacion
 * financiera (tarjetas) si es privada de cada parqueo.
 */

class Usuario extends Authenticatable
{
    use SoftDeletes, Auditable;

    // Nombre exacto de la tabla en la BD
    protected $table = 'usuarios';
    protected $guarded = ['id', 'vigente'];   // vigente la calcula MySQL
    protected $hidden  = ['password', 'remember_token'];

    // Conversión automática de tipos
    protected $casts = [
        'activo'   => 'boolean',
        'password' => 'hashed',
    ];

    // --- Relaciones ---

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }
 
    public function tarjetas()
    {
        return $this->hasMany(Tarjeta::class);
    }
 
    /** Parqueo que administra (solo encargados) */
    public function parqueoAsignado()
    {
        return $this->belongsTo(Parqueo::class, 'parqueo_asignado_id');
    }
 
    /** Parqueo que lo registro por primera vez */
    public function parqueoOrigen()
    {
        return $this->belongsTo(Parqueo::class, 'parqueo_origen_id');
    }
 
    // ---------------- Helpers ----------------
 
    public function esEncargado(): bool
    {
        return $this->rol === 'encargado';
    }
 
    /** Nombre completo para las vistas */
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }
 
    /** Su tarjeta en un parqueo concreto (puede no existir) */
    public function tarjetaEn(int $parqueoId): ?Tarjeta
    {
        return $this->tarjetas()
                    ->withoutGlobalScope('parqueo')
                    ->where('parqueo_id', $parqueoId)
                    ->first();
    }

    // ---------------- Scopes ----------------
 
    public function scopeClientes($query)
    {
        return $query->where('rol', 'usuario');
    }
 
    public function scopeBuscar($query, ?string $texto)
    {
        if (! $texto) {
            return $query;
        }
 
        return $query->where(function ($q) use ($texto) {
            $q->where('ci', 'like', "%{$texto}%")
              ->orWhere('nombre', 'like', "%{$texto}%")
              ->orWhere('apellido', 'like', "%{$texto}%");
        });
    }
}