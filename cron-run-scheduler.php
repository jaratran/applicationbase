<?php
// public_html/cron-run-scheduler.php

// Evita que lo llamen por HTTP, Ejecutable solo por CLI.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

// Opcional: asegúrate de que no se corte por timeout del PHP CLI
@set_time_limit(0);

// Carga del framework
require __DIR__ . '/vendor/autoload.php';
$app    = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Ejecuta el scheduler UNA VEZ (modo cron clásico)
    $status = $kernel->call('schedule:run');

    // Opcional: si quieres ver algo en consola (cron)
    // echo $kernel->output();

    // Salida con el código de estado real
    exit($status);
} catch (Throwable $e) {
    // Log mínimo a archivo dedicado del cron
    $logFile = __DIR__ . '/storage/logs/cron-run-scheduler.log';
    $line    = sprintf(
        "[%s] %s in %s:%d\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
    file_put_contents($logFile, $line, FILE_APPEND);

    // Código de salida != 0 para que el cron lo marque como fallo
    exit(1);
}
