<?php

namespace App\Models\Traits;

use App\Models\Scopes\ParqueoScope;

trait PerteneceAParqueo
{
    // Al arrancar el modelo, le aplica el scope de parqueo automáticamente.
    protected static function bootPerteneceAParqueo(): void
    {
        static::addGlobalScope(new ParqueoScope());
    }
}