<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Auditable;

class Parqueo extends Model
{
    use SoftDeletes, Auditable; 

    protected $table = 'parqueos';

    protected $fillable = [
        'nombre', 'ubicacion', 'capacidad_total',
        'tarifa_moto_periodo', 'tarifa_auto_periodo',
        'horario_apertura', 'horario_cierre',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'capacidad_total'     => 'integer',
        'tarifa_moto_periodo' => 'decimal:2',
        'tarifa_auto_periodo' => 'decimal:2',
    ];

    // --- Relaciones ---
    public function encargado(): HasOne { return $this->hasOne(Encargado::class); }
    public function espacios(): HasMany { return $this->hasMany(Espacio::class); }
    public function saldos(): HasMany   { return $this->hasMany(Saldo::class); }
}
