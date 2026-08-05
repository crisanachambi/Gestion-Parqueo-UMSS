<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditable
{
    // Este método se ejecuta solo cuando el modelo "arranca".
    // Aquí enganchamos los eventos de Eloquent.
    protected static function bootAuditable(): void
    {
        // Al CREAR un registro: guarda quién lo crea.
        static::creating(function ($modelo) {
            if (Auth::check()) {
                $modelo->created_by = Auth::id();
                $modelo->updated_by = Auth::id();
            }
        });

        // Al ACTUALIZAR: guarda quién lo modifica.
        static::updating(function ($modelo) {
            if (Auth::check()) {
                $modelo->updated_by = Auth::id();
            }
        });

        // Al ELIMINAR (soft delete): guarda quién lo borra.
        static::deleting(function ($modelo) {
            if (Auth::check() && ! $modelo->isForceDeleting()) {
                $modelo->deleted_by = Auth::id();
                $modelo->saveQuietly();   // guarda sin disparar más eventos
            }
        });
    }
}