<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Auditable;
use App\Models\Traits\PerteneceAParqueo;

class Espacio extends Model
{
    use SoftDeletes, Auditable, PerteneceAParqueo;

    protected $table = 'espacios';

    protected $fillable = ['parqueo_id', 'numero', 'tipo', 'estado', 'created_by', 'updated_by', 'deleted_by'];

    public function parqueo(): BelongsTo { return $this->belongsTo(Parqueo::class); }
    public function registros(): HasMany { return $this->hasMany(RegistroIngreso::class); }
}
