<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Auditable;

class Vehiculo extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'vehiculos';

    protected $fillable = ['usuario_id', 'placa', 'tipo', 'marca', 'color', 'created_by', 'updated_by', 'deleted_by'];

    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class); }
    public function registros(): HasMany  { return $this->hasMany(RegistroIngreso::class); }
}
