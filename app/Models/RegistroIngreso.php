<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

class RegistroIngreso extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;

    protected $table = 'registros_ingreso';   // Laravel adivinaría 'registro_ingresos', por eso lo fijamos

    protected $fillable = [
        'tarjeta_id', 'vehiculo_id', 'parqueo_id', 'espacio_id',
        'hora_ingreso', 'hora_salida', 'tiempo_permanencia', 'estado',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'hora_ingreso'       => 'datetime',
        'hora_salida'        => 'datetime',
        'tiempo_permanencia' => 'integer',
    ];

    public function tarjeta(): BelongsTo  { return $this->belongsTo(Tarjeta::class); }
    public function vehiculo(): BelongsTo { return $this->belongsTo(Vehiculo::class); }
    public function parqueo(): BelongsTo  { return $this->belongsTo(Parqueo::class); }
    public function espacio(): BelongsTo  { return $this->belongsTo(Espacio::class); }
    // El pago usa 'registro_id' (no el nombre que Laravel adivinaría), lo indicamos:
    public function pago(): HasOne        { return $this->hasOne(Pago::class, 'registro_id'); }
}
