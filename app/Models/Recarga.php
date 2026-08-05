<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

class Recarga extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;

    protected $table = 'recargas';

    protected $fillable = [
        'tarjeta_id', 'encargado_id', 'parqueo_id', 'monto',
        'saldo_anterior', 'saldo_posterior',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'monto'           => 'decimal:2',
        'saldo_anterior'  => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
    ];

    public function tarjeta(): BelongsTo   { return $this->belongsTo(Tarjeta::class); }
    public function encargado(): BelongsTo { return $this->belongsTo(Encargado::class); }
    public function parqueo(): BelongsTo   { return $this->belongsTo(Parqueo::class); }
}
