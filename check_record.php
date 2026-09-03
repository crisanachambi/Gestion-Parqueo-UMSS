<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = App\Models\RegistroIngreso::with(['tarjeta', 'vehiculo', 'espacio', 'parqueo', 'pagos'])->find(6);
if ($r) {
    echo json_encode([
        'registro' => $r->toArray(),
        'estado_espacio' => $r->espacio->estado ?? 'N/A',
        'pagos' => App\Models\Pago::where('registro_id', 6)->count()
    ], JSON_PRETTY_PRINT);
} else {
    echo "Registro no encontrado.";
}
