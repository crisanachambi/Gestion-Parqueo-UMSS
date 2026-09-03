<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

/**
 * PRIVADA de cada parqueo. Aqui vive el saldo.
 * Un usuario tiene una tarjeta distinta por cada parqueo.
 */

class Tarjeta extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;

    protected $table = 'tarjetas';
    protected $guarded = ['id', 'vigente'];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    
    // ---------------- Relaciones ----------------
 
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
 
    public function parqueo()
    {
        return $this->belongsTo(Parqueo::class);
    }
 
    public function registros()
    {
        return $this->hasMany(RegistroIngreso::class);
    }
 
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
 
    public function recargas()
    {
        return $this->hasMany(Recarga::class);
    }
 
    // ---------------- Helpers ----------------
 
    public function estaActiva(): bool
    {
        return $this->estado === 'activa' && $this->deleted_at === null;
    }
 
    public function tieneSaldoPara(float $monto): bool
    {
        return $this->saldo >= $monto;
    }
 
    // ---------------- Scopes ----------------
 
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }
 
    public function scopePorRfid($query, string $codigo)
    {
        return $query->where('codigo_rfid', $codigo);
    }
}
