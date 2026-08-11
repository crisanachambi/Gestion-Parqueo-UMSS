<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Base obligatoria para autenticación
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Auditable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, SoftDeletes, Auditable, Notifiable;

    // Nombre exacto de la tabla en la BD
    protected $table = 'usuarios';

    // Campos asignables de forma masiva
    protected $fillable = [
        'nombre', 
        'email', 
        'password', 
        'rol',
        'telefono', 
        'categoria', 
        'activo',
        'created_by', 
        'updated_by', 
        'deleted_by',
    ];

    // Campos ocultos al serializar
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversión automática de tipos
    protected $casts = [
        'activo'   => 'boolean',
        'password' => 'hashed',
    ];

    // --- Relaciones ---

    // Un usuario puede ser (o no) encargado de un parqueo.
    public function encargado(): HasOne
    {
        return $this->hasOne(Encargado::class);
    }

    // Un usuario tiene muchos vehículos.
    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }

    // Un usuario posee tarjetas.
    public function tarjetas(): HasMany
    {
        return $this->hasMany(Tarjeta::class);
    }
}