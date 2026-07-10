<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Endpoint API REST de ejemplo para obtener usuario autenticado.
// Util en aplicaciones SPA o mobile apps que consumen una API de Laravel

// Estaba en modo Closure y lo comentamos...
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
// });

// Reemplazado por un método de controlador serializable para que route:cache no falle (optimiza-laravel.bat)
// Lo hicimos el 20-Abr-2025, luego el 24-Abr-2025 lo revertimos (¿!?)
// Pero el 01-May-2025 lo volvimos a agregar, así que no lo rerviertas nuevamente.

// Comentamos la línea para evitar que Laravel intente resolver un controlador que no existe
//Route::middleware('auth:api')->get('/user', 'Api\UserController@user');