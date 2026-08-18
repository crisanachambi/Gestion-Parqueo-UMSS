<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Auditable;

class Encargado extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'encargados';

    protected $fillable = ['usuario_id', 'parqueo_id', 'created_by', 'updated_by', 'deleted_by'];

    public function usuario(): BelongsTo { return $this->belongsTo(Usuario::class); }
    public function parqueo(): BelongsTo { return $this->belongsTo(Parqueo::class); }
    public function recargas(): HasMany  { return $this->hasMany(Recarga::class); }
}
