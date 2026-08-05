<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;  // base para login
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    use SoftDeletes;   // habilita el borrado lógico (deleted_at)

    // Laravel pluraliza en inglés; le decimos la tabla real.
    protected $table = 'usuarios';

    // Campos que se pueden asignar de forma masiva (create/update).
    protected $fillable = [
        'nombre', 'email', 'password', 'rol',
        'telefono', 'categoria', 'activo',
        'created_by', 'updated_by', 'deleted_by',
    ];

    // Campos que nunca se muestran al serializar (por seguridad).
    protected $hidden = ['password'];

    // Conversión automática de tipos.
    protected $casts = [
        'activo'   => 'boolean',
        'password' => 'hashed',   // hashea la contraseña automáticamente al asignarla
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
