<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

class Saldo extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;

    protected $table = 'saldos';

    protected $fillable = ['tarjeta_id', 'parqueo_id', 'saldo', 'created_by', 'updated_by', 'deleted_by'];

    protected $casts = ['saldo' => 'decimal:2'];

    public function tarjeta(): BelongsTo { return $this->belongsTo(Tarjeta::class); }
    public function parqueo(): BelongsTo { return $this->belongsTo(Parqueo::class); }
}
