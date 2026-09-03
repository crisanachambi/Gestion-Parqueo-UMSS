<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$record = App\Models\RegistroIngreso::where('id', 6)->where('estado', 'activo')->whereNull('hora_salida')->first();

if ($record) {
    $original_hora = $record->hora_ingreso;
    
    // Update ONLY hora_ingreso
    $record->update(['hora_ingreso' => '2026-08-20 16:14:36']);
    $record->refresh();
    
    echo json_encode([
        'original' => $original_hora,
        'updated' => [
            'id' => $record->id,
            'hora_ingreso' => $record->hora_ingreso,
            'hora_salida' => $record->hora_salida,
            'estado' => $record->estado,
            'periodos_usados' => $record->periodos_usados
        ]
    ], JSON_PRETTY_PRINT);
} else {
    echo "No se encontro el registro o no cumple las condiciones.";
}
