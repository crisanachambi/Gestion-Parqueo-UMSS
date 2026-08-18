<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ParqueoScope implements Scope
{
    /**
     * Filtra automáticamente las consultas del modelo por el parqueo
     * del encargado autenticado.
     *
     * Si no hay usuario autenticado, o el usuario autenticado no es
     * encargado de ningún parqueo (por ejemplo, rol = 'usuario'),
     * no se aplica ninguna restricción — el scope no filtra nada.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $encargado = Auth::user()->encargado;

        if (! $encargado) {
            return;
        }

        $builder->where($model->getTable() . '.parqueo_id', $encargado->parqueo_id);
    }
}