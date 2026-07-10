<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect(\App\Providers\RouteServiceProvider::HOME); // Redirige a la constante HOME definida en app\Providers\RouteServiceProvider.php (definimos la página del panel)
        }

        return $next($request);
    }
}
