<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\Parametros\ParameterController;
use App\Http\Controllers\Parametros\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Actores\UsuarioController;
use App\Http\Controllers\Actores\EmpresaController;
use App\Http\Controllers\Actores\SucursalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [HomeController::class, 'index']);

// === AUTENTICACIÓN Y VERIFICACIÓN DE CUENTA ===================================

// Login y Logout
Route::get('login',   [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login',  [LoginController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');


// Activación de cuenta - Correo de Bienvenida : Enlace que el usuario abre desde el correo electrónico y ruta de reenvío del correo (resend)
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend',            [VerificationController::class, 'resend'])->name('verification.resend');


// Activación de cuenta - Definición de Contraseña : Definición de contraseña del usuario y ruta de guardado de la contraseña y activación del usuario
Route::get('user/password/{token}', [AuthController::class, 'passwordUser'])->name('user.password');
Route::post('user/activation',      [AuthController::class, 'activateUser'])->name('user.activate');


// Olvidé la clave - Solicitud de correo para recuperar contraseña y ruta que guarda solicitud y envía el correo
Route::get('forget-password',  [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');


// Redefinir contraseña - Enlace que abre el usuario cuando hace click en correo de recuperación y ruta que guarda la nueva contraseña
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');


// === MÓDULOS PRINCIPALES DEL SISTEMA ==========================================
Route::middleware(['auth'])->group(function () {

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// PANEL DE CONTROL GENÉRICO - Esto es para todos los usuarios autenticados
	Route::get('panel', [PanelController::class, 'index'])->name('panel.index');

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// PERFIL
	// Esto es para todos los usuarios
	Route::get('perfil/password/{id}', [ProfileController::class, 'password'])->name('perfil.password');
	Route::post('perfil/password',     [ProfileController::class, 'updatePassword'])->name('perfil.password.update');
	Route::resource('perfil', ProfileController::class)->only(['index', 'store', 'edit', 'update']);

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// PARÁMETROS GENERALES - LISTAS Y SELECCION DE CATÁLOGOS, REGIONES y COMUNAS
	// Esto es para todos los usuarios
	Route::get('parametros/catalogo/{id}',    [ParameterController::class, 'listaCatalogo']);
	Route::get('parametros/comuna',           [LocationController::class, 'obtenerComuna']);
	Route::get('parametros/region',           [LocationController::class, 'obtenerRegion']);
	Route::get('parametros/region-operativa', [LocationController::class, 'obtenerRegionOperativa']);

	// PARÁMETROS GENERALES - ADMINISTRACION Y MANTENCIÓN
	// Esto es sólo para Admin-IT
	Route::middleware(['check.role:'   . config('constantes.ROL_ADMINISTRADOR_IT')])->group(function () {
		Route::get('admin/parameters',         [ParameterController::class, 'index'])->name('parameters.index');
		Route::post('admin/parameters/update', [ParameterController::class, 'update'])->name('parameters.update');
	});

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// MANTENEDORES DE ACTORES
	// Esto es sólo para Admin-IT y Coordinadores X.
	Route::middleware(['check.role:'   . config('constantes.ROL_COORDINADOR') . ','
										. config('constantes.ROL_ADMINISTRADOR_IT')])->group(function () {


		// Usuarios, Empresas y Sucursales comparten esta restricción de roles.
		Route::post('usuario/{id}/resend-welcome', [UsuarioController::class, 'resendWelcomeEmail'])->name('usuarios.resend-welcome');
		Route::resource('actores/usuario', UsuarioController::class);

		//----------------------------------------------------------------------------------------------------------------------------------------------
		Route::get('empresa/{id}/plantas',       [EmpresaController::class, 'plantas'])->name('empresa.plantas');                // En vinculación con Plantas de Proceso
		Route::post('empresa/{empresa}/plantas', [EmpresaController::class, 'guardarPlantas'])->name('empresa.plantas.guardar'); // En vinculación con Plantas de Proceso
		Route::get('empresas/tipo/{id}/por-rol', [EmpresaController::class, 'obtenerEmpresasPorTipoYRol']);                      // Uso exclusivo mantenedor de usuarios
		Route::resource('actores/empresa', EmpresaController::class)->names([
			'index'   => 'empresa.index',
			'create'  => 'empresa.create',
			'store'   => 'empresa.store',
			'show'    => 'empresa.show',
			'edit'    => 'empresa.edit',
			'update'  => 'empresa.update',
			'destroy' => 'empresa.destroy',
		]);

		//----------------------------------------------------------------------------------------------------------------------------------------------
		Route::get('sucursal/{id}/productoras',        [SucursalController::class, 'productoras'])->name('sucursal.productoras');                // En vinculación con Productoras de Materia Prima
		Route::post('sucursal/{sucursal}/productoras', [SucursalController::class, 'guardarProductoras'])->name('sucursal.productoras.guardar'); // En vinculación con Productoras de Materia Prima
		Route::get('sucursales/tipo/{id}/por-rol',     [SucursalController::class, 'obtenerSucursalesPorTipoYRol']);                             // Uso exclusivo mantenedor de usuarios
		Route::resource('actores/sucursal', SucursalController::class)->names([
			'index'   => 'sucursal.index',
			'create'  => 'sucursal.create',
			'store'   => 'sucursal.store',
			'show'    => 'sucursal.show',
			'edit'    => 'sucursal.edit',
			'update'  => 'sucursal.update',
			'destroy' => 'sucursal.destroy',
		]);
	});

	//----------------------------------------------------------------------------------------------------------------------------------------------
});
