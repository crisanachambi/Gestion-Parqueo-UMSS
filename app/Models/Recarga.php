<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

/**
 * Carga de saldo hecha por el encargado.
 * La escribe la web; la app movil solo la lee.
 */

class Recarga extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;
 
    protected $table = 'recargas';
    protected $guarded = ['id'];
 
    protected $casts = [
        'monto'           => 'decimal:2',
        'saldo_anterior'  => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
        'fecha_recarga'   => 'datetime',
    ];

    // ---------------- Relaciones ----------------
    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class);
    }
 
    public function parqueo()
    {
        return $this->belongsTo(Parqueo::class);
    }
 
    public function encargado()
    {
        return $this->belongsTo(Usuario::class, 'encargado_id');
    }
 
    // ---------------- Scopes ----------------
 
    public function scopeExitosas($query)
    {
        return $query->where('estado', 'exitosa');
    }
 
    public function scopeDeHoy($query)
    {
        return $query->whereDate('fecha_recarga', today());
    }
}
