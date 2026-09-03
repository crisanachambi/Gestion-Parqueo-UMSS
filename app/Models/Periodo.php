<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periodo extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;
 
    protected $table = 'periodos';
    protected $guarded = ['id'];
 
    protected $casts = [
        'orden' => 'integer',
    ];

    public function parqueo()
    {
        return $this->belongsTo(Parqueo::class);
    }
 
    /** Etiqueta para las vistas: "Manana (06:30 - 11:30)" */
    public function getEtiquetaAttribute(): string
    {
        $inicio = substr($this->hora_inicio, 0, 5);
        $fin    = substr($this->hora_fin, 0, 5);
 
        return "{$this->nombre} ({$inicio} - {$fin})";
    }
 
    /**
     * Los bordes no cuentan: entrar a las 11:30 exactas no
     * incluye el periodo que termina a esa hora.
     */
    public function scopeQueSolapan($query, string $desde, string $hasta)
    {
        return $query->where('hora_inicio', '<', $hasta)
                     ->where('hora_fin', '>', $desde);
    }
}
