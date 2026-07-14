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
use App\Http\Controllers\Actores\CamionController;
use App\Http\Controllers\Actores\ConductorController;
use App\Http\Controllers\Actores\RamplaController;
use App\Http\Controllers\SolicitudesRetiroController;
use App\Http\Controllers\PlanificacionesRetiroController;
use App\Http\Controllers\ProgramaDiarioController;

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
//Route::get('/', 'HomeController@index'); // Muestra página inicial según autenticación, pero serializable para que route:cache no falle
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
	Route::resource('perfil', ProfileController::class);

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
	// Esto es sólo para Admin-IT, Coodinadores X y Coordinador XII
	Route::middleware(['check.role:'   . config('constantes.ROL_COORDINADOR') . ','
										. config('constantes.ROL_COORDINADOR_XII') . ','
										. config('constantes.ROL_ADMINISTRADOR_IT')])->group(function () {


		// PERO esto es sólo para los 2 roles originales: Admin-IT y Coodinadores
		Route::middleware(['check.role:'   . config('constantes.ROL_COORDINADOR') . ','
											. config('constantes.ROL_ADMINISTRADOR_IT')])->group(function () {

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

		// Esto es para los 3 roles: Admin-IT, Coodinadores X y Coordinador XII
		//----------------------------------------------------------------------------------------------------------------------------------------------
		Route::post('conductores/{conductor}/telegram/generar-pin', [ConductorController::class, 'generarPinTelegram'])->name('conductores.telegram.generar-pin');
		Route::post('conductores/{conductor}/telegram/desvincular', [ConductorController::class, 'desvincularTelegram'])->name('conductores.telegram.desvincular');
		// Route::get('conductores/empresa/{id}',                      [ConductorController::class, 'obtenerConductoresPorEmpresa']);
		Route::resource('actores/conductor', ConductorController::class)->names([
			'index'   => 'conductor.index',
			'create'  => 'conductor.create',
			'store'   => 'conductor.store',
			'show'    => 'conductor.show',
			'edit'    => 'conductor.edit',
			'update'  => 'conductor.update',
			'destroy' => 'conductor.destroy',
		]);

		//----------------------------------------------------------------------------------------------------------------------------------------------
		Route::get('camiones/detalle/{id}', [CamionController::class, 'detalleCamion'])->name('camiones.detalle');
		Route::get('camiones',              [CamionController::class, 'obtenerCamiones']);
		// Route::get('camiones/region-operativa/{id}',  [CamionController::class, 'obtenerCamionesPorRegionOperativa'])->name('camiones.por-region-operativa');
		Route::get('camiones/empresa/{id}', [CamionController::class, 'obtenerCamionesPorEmpresa']);
		Route::get('camiones/tipo/{id}',    [CamionController::class, 'obtenerCamionesPorTipo'])->name('camiones.por-tipo');
		Route::resource('actores/camion', CamionController::class)->names([
			'index'   => 'camion.index',
			'create'  => 'camion.create',
			'store'   => 'camion.store',
			'show'    => 'camion.show',
			'edit'    => 'camion.edit',
			'update'  => 'camion.update',
			'destroy' => 'camion.destroy',
		]);

		//----------------------------------------------------------------------------------------------------------------------------------------------
		// Route::get('ramplas',                                           [RamplaController::class, 'obtenerRamplas']);
		// Route::get('ramplas/estados-por-transporte/{tipoTransporteId}', [RamplaController::class, 'estadosPorTipoTransporte']);
		Route::resource('actores/rampla', RamplaController::class)->names([
			'index'   => 'rampla.index',
			'create'  => 'rampla.create',
			'store'   => 'rampla.store',
			'show'    => 'rampla.show',
			'edit'    => 'rampla.edit',
			'update'  => 'rampla.update',
			'destroy' => 'rampla.destroy',
		]);
	});

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// SOLICITUDES DE RETIRO DE MATERIA PRIMA - CREAR, EDITAR-ACTUALIZAR y ELIMINAR (desactivar) SOLICITUD DE RETIRO
	// La CREACION es sólo para Admin-IT y Solicitantes (planta y productor) - 15-07-25: Se excluye Cordinador para CREAR solicitudes (Para eso tienen la Manual).
	Route::middleware(['check.role:'    . config('constantes.ROL_ADMINISTRADOR_IT') . ','
										. config('constantes.ROL_SOLICITANTE_PLANTA') . ','
										. config('constantes.ROL_SOLICITANTE_PLANTA_XII') . ','
										. config('constantes.ROL_SOLICITANTE_PRODUCTOR')])->group(function () {

		Route::get('solicitudes-retiro/create',    [SolicitudesRetiroController::class, 'create'])->name('solicitudes-retiro.create');
		Route::post('solicitudes-retiro',          [SolicitudesRetiroController::class, 'store'])->name('solicitudes-retiro.store');
	});

	// EDITAR, ANULAR es para Admin-IT, Coodinadores y Solicitantes (planta y productor)
	Route::middleware(['check.role:'    . config('constantes.ROL_COORDINADOR') . ','
										. config('constantes.ROL_ADMINISTRADOR_IT') . ','
										. config('constantes.ROL_SOLICITANTE_PLANTA') . ','
										. config('constantes.ROL_SOLICITANTE_PLANTA_XII') . ','
										. config('constantes.ROL_SOLICITANTE_PRODUCTOR')])->group(function () {

		Route::get('solicitudes-retiro/{id}/edit', [SolicitudesRetiroController::class, 'edit'])->name('solicitudes-retiro.edit');
		Route::put('solicitudes-retiro/{id}',      [SolicitudesRetiroController::class, 'update'])->name('solicitudes-retiro.update');
		Route::delete('solicitudes-retiro/{id}',   [SolicitudesRetiroController::class, 'destroy'])->name('solicitudes-retiro.destroy');
	});

	// SOLICITUDES DE RETIRO DE MATERIA PRIMA - APROBAR y COMENTAR SOLICITUD DE RETIRO
	// Esto es sólo para Admin-IT y Coodinadores
	Route::middleware(['check.role:'	. config('constantes.ROL_COORDINADOR') . ','
										. config('constantes.ROL_COORDINADOR_XII') . ','
										. config('constantes.ROL_ADMINISTRADOR_IT')])->group(function () {

		Route::post('solicitudes-retiro/{id}/aprobar',  [SolicitudesRetiroController::class, 'aprobarRetiro'])->name('solicitudes-retiro.aprobar');
		Route::post('solicitudes-retiro/{id}/comentar', [SolicitudesRetiroController::class, 'comentarRetiro'])->name('solicitudes-retiro.comentar');
	});

	// SOLICITUDES DE RETIRO DE MATERIA PRIMA - EXPLORAR: LISTADO GENERAL / SELECCION DESDE CORREO y EXAMINAR EJEMPLAR
	// Esto es para todos los usuarios
	Route::get('solicitudes-retiro',      [SolicitudesRetiroController::class, 'index'])->name('solicitudes-retiro.index');
	Route::get('/ver-solicitud/{token}',  [SolicitudesRetiroController::class, 'verDesdeToken'])->name('solicitudes-retiro.ver-desde-token');
	Route::get('solicitudes-retiro/{id}', [SolicitudesRetiroController::class, 'show'])->name('solicitudes-retiro.show');

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// CREACIÓN de SOLICITUDES y CREACION MANUAL de PLANIFICACIONES y CIERRE DE PLANIFICACIONES (Ingreso de TICKET)
	// LISTADOS DE PLANTAS y/o EMPRESAS VINCULADAS EN MAQUILAS
	Route::middleware(['check.role:'	. config('constantes.ROL_SOLICITANTE_PLANTA')    . ',' . config('constantes.ROL_SOLICITANTE_PLANTA_XII') . ','
										. config('constantes.ROL_SOLICITANTE_PRODUCTOR') . ','
										. config('constantes.ROL_COORDINADOR')           . ',' . config('constantes.ROL_COORDINADOR_XII') . ','
										. config('constantes.ROL_ADMINISTRADOR_IT') ])->group(function () {

		Route::get('productora/{id}/plantas-vinculadas', [EmpresaController::class,  'plantasVinculadas'])->name('productora.plantas-vinculadas');     // Para select2 de create/edit en Solicitudes y creación Manual de Planificacion
		Route::get('planta/{id}/productoras-vinculadas', [SucursalController::class, 'productorasVinculadas'])->name('planta.productoras-vinculadas'); // Para select2 de create/edit en Solicitudes y creación Manual de Planificacion

		Route::get('empresas/tipo/{id}'                , [EmpresaController::class,   'obtenerEmpresasPorTipo']);                                      // En create/edit de: Conductores, Camiones, Solicitudes de Retiro (SOLO rol AdminIT/Coordinador).
		Route::get('sucursales/tipo/{id}'              , [SucursalController::class,  'obtenerSucursalesPorTipo']);                                    // En create/edit de: Solicitudes de Retiro (SOLO rol AdminIT/Coordinador).
	});

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// PLANIFICACIÓN DE RETIROS DE MATERIA PRIMA - CREACIÓN MANUAL DE PLANIFICACIÓN DE RETIRO y CIERRE DE PLANIFICACIONES (Ingreso de TICKET)
	// Esto es sólo para Admin-IT y Coodinadores
	Route::middleware(['check.role:'    . config('constantes.ROL_COORDINADOR') . ',' . config('constantes.ROL_COORDINADOR_XII') . ','
										. config('constantes.ROL_ADMINISTRADOR_IT')])->group(function () {

		// AJAXs de apoyo a planificación manual
		Route::get('ramplas',												[RamplaController::class,    'obtenerRamplas']);
		Route::get('ramplas/estados-por-transporte/{tipoTransporteId}',		[RamplaController::class,    'estadosPorTipoTransporte']);
		Route::get('camiones/region-operativa/{id}',						[CamionController::class,    'obtenerCamionesPorRegionOperativa']);
		Route::get('conductores/empresa/{id}',								[ConductorController::class, 'obtenerConductoresPorEmpresa']);

		Route::get('planificaciones-retiro/create-manual', [PlanificacionesRetiroController::class, 'createManual'])->name('planificaciones-retiro.create-manual');
		Route::post('planificaciones-retiro/store-manual', [PlanificacionesRetiroController::class, 'storeManual'])->name('planificaciones-retiro.store-manual');

		Route::get('planificaciones-retiro/{id}/edit', [PlanificacionesRetiroController::class, 'edit'])->name('planificaciones-retiro.edit');
		Route::put('planificaciones-retiro/{id}',      [PlanificacionesRetiroController::class, 'update'])->name('planificaciones-retiro.update');
		Route::delete('planificaciones-retiro/{id}',   [PlanificacionesRetiroController::class, 'destroy'])->name('planificaciones-retiro.destroy');

		Route::post('planificaciones-retiro/{id}/cerrar',  [PlanificacionesRetiroController::class, 'cerrarPlanificacion'])->name('planificaciones-retiro.cerrar');
	});

	// PLANIFICACIÓN DE RETIROS DE MATERIA PRIMA - EXPLORAR: LISTADO GENERAL / SELECCION DESDE CORREO y EXAMINAR EJEMPLAR
	// Esto es para todos los usuarios
	Route::get('planificaciones-retiro',      [PlanificacionesRetiroController::class, 'index'])->name('planificaciones-retiro.index');
	Route::get('/ver-planificacion/{token}',  [PlanificacionesRetiroController::class, 'verDesdeToken'])->name('planificaciones-retiro.ver-desde-token');
	Route::get('planificaciones-retiro/{id}', [PlanificacionesRetiroController::class, 'show'])->name('planificaciones-retiro.show');

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// PROGRAMAS DIARIOS
	// Esto es sólo para Admin-IT y Coodinadores
	Route::middleware(['check.role:' . config('constantes.ROL_COORDINADOR') . ','
										. config('constantes.ROL_ADMINISTRADOR_IT')])->group(function () {

		Route::get('programa-diario/preparar-emision',      [ProgramaDiarioController::class, 'create'])->name('programa-diario.preparar-emision');
		Route::get('programa-diario/previsualizar-emision', [ProgramaDiarioController::class, 'previsualizarEmision'])->name('programa-diario.previsualizar-emision');
		Route::post('programa-diario/efectuar-emision',     [ProgramaDiarioController::class, 'store'])->name('programa-diario.efectuar-emision');
	});

	//----------------------------------------------------------------------------------------------------------------------------------------------
	// VISUALIZACIÓN DE PROGRAMAS DIARIOS (abierto a roles internos)
	Route::middleware(['check.role:' . config('constantes.ROL_COORDINADOR')       . ',' . config('constantes.ROL_ADMINISTRADOR_IT') . ','
										. config('constantes.ROL_PERSONAL_GERENCIA') . ',' . config('constantes.ROL_PERSONAL_PRODUCCION') . ','
										. config('constantes.ROL_PERSONAL_CALIDAD')  . ',' . config('constantes.ROL_PERSONAL_MANTENCION') . ','
										. config('constantes.ROL_PERSONAL_ROMANA')])->group(function () {

		Route::get('programa-diario',                       [ProgramaDiarioController::class, 'index'])->name('programa-diario.index');
		Route::get('programa-diario/{fecha}/ver',           [ProgramaDiarioController::class, 'show'])->name('programa-diario.ver');
		Route::get('programa-diario/consolidados/{fecha}',  [ProgramaDiarioController::class, 'consolidados'])->name('programa-diario.consolidados');

		// 🆕 Nueva ruta con token cifrado para acceder desde el correo
		Route::get('programa-diario/ver-desde-token/{token}', [ProgramaDiarioController::class, 'verDesdeToken'])->name('programa-diario.ver-desde-token');
	});
});
