<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ParqueoScope implements Scope
{
    // Este método se aplica AUTOMÁTICAMENTE a toda consulta del modelo.
    public function apply(Builder $builder, Model $model): void
    {
        // Si no hay nadie logueado (ej: seeders, comandos), no filtramos.
        if (! Auth::check()) {
            return;
        }

        $usuario = Auth::user();

        // Buscamos de qué parqueo es encargado el usuario logueado.
        // (usuario -> encargado -> parqueo_id)
        $parqueoId = $usuario->encargado?->parqueo_id;

        // Si es un encargado con parqueo, filtramos por ese parqueo.
        if ($parqueoId) {
            $builder->where($model->getTable() . '.parqueo_id', $parqueoId);
        }
    }
}