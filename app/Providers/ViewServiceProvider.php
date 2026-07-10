<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Models\DesignParameter;

use App\Models\OperationalParameter;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Inyectar parámetros visuales en TODAS las vistas, si hay conexión válida a la base de datos
        View::composer('*', function ($view) {
            try {
                $param = DesignParameter::first();
                $view->with('designParameter', $param);
            } catch (\Throwable $e) {
                Log::warning('No se pudo cargar parámetros de diseño: ' . $e->getMessage());
                $view->with('designParameter', null); // Valor nulo evita error en vistas
            }
        });

        // Privado: Solo para vistas con usuario autenticado
        View::composer('*', function ($view) {
            if (Auth::check()) {
                try {
                    $op = OperationalParameter::first();
                    $view->with('operationalParameter', $op);
                } catch (\Throwable $e) {
                    Log::warning('No se pudo cargar parámetros operacionales: ' . $e->getMessage());
                    $view->with('operationalParameter', null);
                }
            }
        });
    }
}
