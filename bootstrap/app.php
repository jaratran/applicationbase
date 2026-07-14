<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('telegram:procesar-mensajes')
                ->everyMinute()
                ->withoutOverlapping();   // evita solapes si una ejecución demora

    })

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'signed_custom' => \App\Http\Middleware\CustomValidateSignature::class,
            'check.role'    => \App\Http\Middleware\CheckRoleAccess::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    
    ->create();
