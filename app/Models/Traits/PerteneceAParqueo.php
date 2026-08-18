<?php

namespace App\Models\Traits;

use App\Models\Scopes\ParqueoScope;
use Illuminate\Support\Facades\Auth;

trait PerteneceAParqueo
{
    protected static function bootPerteneceAParqueo(): void
    {
        // Filtra TODA consulta por el parqueo del encargado,
        // aunque el controlador se olvide de hacerlo.
        static::addGlobalScope('parqueo', function ($query) {
            if ($parqueoId = self::parqueoActual()) {
                $tabla = $query->getModel()->getTable();
                $query->where("{$tabla}.parqueo_id", $parqueoId);
            }
        });

        // Rellena parqueo_id solo al crear.
        static::creating(function ($modelo) {
            $modelo->parqueo_id ??= self::parqueoActual();
        });
    }

    /**
     * El parqueo del encargado logueado.
     * Devuelve null sin sesión (seeders, consola, API móvil),
     * y en ese caso el scope no filtra nada.
     */
    protected static function parqueoActual(): ?int
    {
        return Auth::check() ? Auth::user()->parqueo_asignado_id : null;
    }

    /**
     * Escape puntual para ver los 3 parqueos.
     * Uso: Espacio::sinFiltroParqueo()->get()
     * Lo necesita la API móvil.
     */

    public function scopeSinFiltroParqueo($query)
    {
        return $query->withoutGlobalScope('parqueo');
    }
}