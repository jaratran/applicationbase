<?php
namespace App\Http\Controllers\Actores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Traits\ProcesaAvatarTrait;
use App\Notifications\CustomVerifyWelcomeEmail;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Services\UserRoleAssignment;

class UsuarioController extends Controller
{
    // Utilizamos el trait que procesa y almacena imagenes de Avatar
    use ProcesaAvatarTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserRoleAssignment $roleAssignment)
    {
        $usuario = User::with([
                                'rol:id,nombre',
                                'empresa:id,razon_social',
                                'sucursal:id,nombre_sucursal'
                            ])
                            // ->where('activo', 1) -- Se omite este filtro para que salgan todos.
                            ->get([
                                'activo',
                                'id',
                                'rut_usuario',
                                'nombre_usuario',
                                'apellidos_usuario',
                                'empresa_id',
                                'sucursal_id',
                                'rol_id',
                                'email',
                                'telefono',
                                'avatar',
                                'email_verified_at' // 👈🏼 ESTA ES LA CLAVE
                            ]);

        $manageableUserIds = $usuario
            ->filter(fn (User $user) => $roleAssignment->canManageUser(Auth::user(), $user))
            ->pluck('id');

        return view('actores.usuario.index', [
            'usuario' => $usuario,
            'manageableUserIds' => $manageableUserIds,
        ]);
    } 

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(UserRoleAssignment $roleAssignment)
    {
        return view("actores/usuario.create", [
            'roles' => $roleAssignment->assignableRoles(Auth::user()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, UserRoleAssignment $roleAssignment)
    {
        $assignableRoleIds = $roleAssignment->assignableRoles(Auth::user())->pluck('id');

        // Se mantiene el campo 'comuna' por compatibilidad con los blades actuales.
        // Este valor es luego asignado al modelo en 'comuna_id'.
        $request->validate([
            'rut_usuario'        => 'required|string|max:12|unique:users,rut_usuario',
            'nombre_usuario'     => 'required|string|max:255',
            'apellidos_usuario'  => 'required|string|max:255',
            'rol_id'             => ['required', 'integer', Rule::in($assignableRoleIds)],
            'email'              => 'required|email|max:255|unique:users,email',
            'telefono'           => 'required|string|max:30',
            'comuna'             => 'required|integer|exists:comunas,id',
            'direccion'          => 'required|string|max:255',
            'avatar'             => 'nullable|mimetypes:image/jpeg,image/png,image/gif,image/bmp|max:2048',
        ]);

        $rol = intval($request->input('rol_id'));

        if ($rol === config('constantes.ROL_SOLICITANTE_PLANTA')) {
            $request->validate([
                'sucursal_id' => 'required|integer|exists:sucursales,id',
            ]);
        }
        if ($rol === config('constantes.ROL_SOLICITANTE_PRODUCTOR')) {
            $request->validate([
                'empresa_id' => 'required|integer|exists:empresas,id',
            ]);
        }

        try {

            $usuario = new User();
            $usuario->rut_usuario = $request->rut_usuario;
            $usuario->nombre_usuario = $request->nombre_usuario;
            $usuario->apellidos_usuario = $request->apellidos_usuario;
            $usuario->sucursal_id = $request->sucursal_id;
            $usuario->empresa_id = $request->empresa_id;
            $usuario->rol_id = $request->rol_id;
            $usuario->email = $request->email;
            $usuario->telefono = $request->telefono;
            $usuario->comuna_id = $request->comuna;
            $usuario->direccion = $request->direccion;

            $usuario->activo = 1; // Por defecto, el usuario se crea vigente

            $usuario->save();

            // Procesa avatar y si todo OK incluso guarda su nombre en el perfil del usuario
            if ($request->hasFile('avatar')) {
                $resultado = $this->procesarAvatar($request->file('avatar'), $usuario);

                if (!$resultado['success']) {
                    Log::error('❌ Error al procesar avatar de usuario', [
                        'email'     => $usuario->email,
                        'usuarioId' => $usuario->id,
                        'mensaje'   => $resultado['message'],
                    ]);

                    return redirect()->back()->withErrors(['avatar' => __('validation.custom.avatar.processing_error', ['message' => $resultado['message']])]);
                }
            }

            // Disparo directo de la notificación personalizada.
            // Se evita el uso de MustVerifyEmail y del evento Registered para tener control completo.
            // event(new Registered($usuario)); // Laravel lanzará el proceso de verificación - Evitamos esta forma hasta que corrigamos el doble envío de correos
            $usuario->notify(new CustomVerifyWelcomeEmail($usuario)); // Envía el correo personalizado de bienvenida y activación.

            // El siguiente mensaje interpola el email del usuario recién creado. Revisar el archivo resources/lang/es/auth.php para ver cómo se define.
            return redirect('actores/usuario')->with('status', __('auth.user_created_and_activation_sent', ['email' => $request->email] )); // Mensaje definido en resources/lang/es/auth.php

        } catch (\Throwable $e) {
            Log::error('❌ Error al registrar nuevo usuario', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                        ->with('error', __('auth.user_creation_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // NOTA: Se mantiene el campo 'comuna' por compatibilidad con los blades actuales.
        // Este valor es luego asignado al modelo en 'comuna_id'.
        $usuario = User::with([
                                'rol:id,nombre',
                                'empresa:id,razon_social',
                                'sucursal:id,nombre_sucursal',
                                'comuna:id,nombre,region_id',
                                'comuna.region:id,nombre'
                            ])
                            // ->where('activo', 1) -- Se omite este filtro para que salgan todos.
                            ->where("id", $id)
                            ->first([
                                'id',
                                'activo',
                                'rut_usuario',
                                'nombre_usuario',
                                'apellidos_usuario',
                                'empresa_id',
                                'sucursal_id',
                                'rol_id',
                                'email',
                                'telefono',
                                'avatar',
                                'direccion',
                                'comuna_id',
                                'observacion_inactividad'
                            ]);

        return response()->json($usuario);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, UserRoleAssignment $roleAssignment)
    {
        $ide = Crypt::decrypt($id);

        // NOTA: Se mantiene el campo 'comuna' por compatibilidad con los blades actuales.
        // Este valor es luego asignado al modelo en 'comuna_id'.
        $usuario = User::with([
                                'rol:id,nombre',
                                'empresa:id,razon_social',
                                'sucursal:id,nombre_sucursal',
                                'comuna:id,nombre,region_id',
                                'comuna.region:id,nombre'
                            ])
                            // ->where('activo', 1) -- Se omite este filtro para que salgan todos.
                            ->where("id", $ide)
                            ->first([
                                'id',
                                'rut_usuario',
                                'nombre_usuario',
                                'apellidos_usuario',
                                'empresa_id',
                                'sucursal_id',
                                'rol_id',
                                'email',
                                'telefono',
                                'avatar',
                                'direccion',
                                'comuna_id'
                            ]);

        abort_unless($roleAssignment->canManageUser(Auth::user(), $usuario), 403);

        return view('actores/usuario.edit', [
            'usuario' => $usuario,
            'roles' => $roleAssignment->assignableRoles(Auth::user(), $usuario),
            'canChangeRole' => $roleAssignment->canChangeRole(Auth::user(), $usuario),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, UserRoleAssignment $roleAssignment)
    {
        $usuario = User::findOrFail($id);
        abort_unless($roleAssignment->canManageUser(Auth::user(), $usuario), 403);

        $assignableRoleIds = $roleAssignment->assignableRoles(Auth::user(), $usuario)->pluck('id');

        // NOTA: Se mantiene el campo 'comuna' por compatibilidad con los blades actuales.
        // Este valor es luego asignado al modelo en 'comuna_id'.
        $request->validate([
            'rut_usuario'        => 'required|string|max:12|unique:users,rut_usuario,' . $id,
            'nombre_usuario'     => 'required|string|max:255',
            'apellidos_usuario'  => 'required|string|max:255',
            'rol_id'             => ['required', 'integer', Rule::in($assignableRoleIds)],
            'email'              => 'required|email|max:255|unique:users,email,' . $id,
            'telefono'           => 'required|string|max:30',
            'comuna'             => 'required|integer|exists:comunas,id',
            'direccion'          => 'required|string|max:255',
            'avatar'             => 'nullable|mimetypes:image/jpeg,image/png,image/gif,image/bmp|max:2048',
        ]);

        $rol = intval($request->input('rol_id'));

        if ($rol === config('constantes.ROL_SOLICITANTE_PLANTA')  ) {
            $request->validate([
                'sucursal_id' => 'required|integer|exists:sucursales,id',
            ]);
        }
        if ($rol === config('constantes.ROL_SOLICITANTE_PRODUCTOR') ) {
            $request->validate([
                'empresa_id' => 'required|integer|exists:empresas,id',
            ]);
        }

        try {
            $usuario->rut_usuario = $request->rut_usuario;
            $usuario->nombre_usuario = $request->nombre_usuario;
            $usuario->apellidos_usuario = $request->apellidos_usuario;
            $usuario->sucursal_id = $request->sucursal_id;
            $usuario->empresa_id = $request->empresa_id;
            $usuario->rol_id = $request->rol_id;
            $usuario->email = $request->email;
            $usuario->telefono = $request->telefono;
            $usuario->comuna_id = $request->comuna;
            $usuario->direccion = $request->direccion;
            $usuario->save();
    
            // Procesa avatar y si todo OK incluso guarda su nombre en el perfil del usuario
            if ($request->hasFile('avatar')) {
                $resultado = $this->procesarAvatar($request->file('avatar'), $usuario);
            
                if (!$resultado['success']) {
                    return redirect()->back()->withErrors(['avatar' => __('validation.custom.avatar.processing_error', ['message' => $resultado['message']])]);
                }
            }
    
            return redirect('actores/usuario')->with('status', __('auth.user_updated_successfully'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al actualizar usuario', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                        ->with('error', __('auth.user_update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $usuario = User::findOrFail($id);
    
            if($usuario->activo){ // Si usuario está activo quiere decir que estamos desactivando y requerimos observación_inactividad
                $request->validate([
                    'observacion_inactividad' => 'required|string|max:500',
                ]);
    
                $usuario->observacion_inactividad = $request->input('observacion_inactividad');
            }
    
            $usuario->activo = !($usuario->activo);
            $usuario->save();
    
            return redirect()->route('usuario.index')->with('status', __('auth.user_status_changed', ['status' => $usuario->activo ? 'activado' : 'desactivado',]));

        } catch (\Throwable $e) {
            Log::error('❌ Error al cambiar estado de usuario', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', __('auth.user_status_change_error'));
        }
    }

    public function preview(Request $request) {
        //
    }

    public function print($id){
        //
    }

    public function resendWelcomeEmail($id)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => __('auth.email_already_verified')], 422); // Mensaje definido en resources/lang/es/auth.php
        }

        // Disparo directo de la notificación personalizada.
        // Se evita el uso de MustVerifyEmail y del evento Registered para tener control completo.
        // event(new Registered($user)); // Laravel lanzará el proceso de verificación - Evitamos esta forma hasta que corrigamos el doble envío de correos
        $user->notify(new \App\Notifications\CustomVerifyWelcomeEmail($user)); // Vuelve a enviar el correo personalizado de bienvenida y activación.

        return response()->json(['message' => __('auth.welcome_successfully_sent')]); // Mensaje definido en resources/lang/es/auth.php
    }
}
