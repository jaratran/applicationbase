<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

use App\Traits\ProcesaAvatarTrait;

use App\Models\User;

class ProfileController extends Controller
{
    // Utilizamos el trait que procesa y almacena imagenes de Avatar
    use ProcesaAvatarTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = User::with([
                            'rol:id,nombre',
                            'sucursal:id,nombre_sucursal',
                            'empresa:id,razon_social',
                            'comuna:id,nombre,region_id',
                            'comuna.region:id,nombre'
                        ])
                        ->where('activo', 1)
                        ->where("id", Auth::user()->id)
                        ->first([
                            'id',
                            'rut_usuario',
                            'nombre_usuario',
                            'apellidos_usuario',
                            'sucursal_id',
                            'empresa_id',
                            'rol_id',
                            'email',
                            'telefono',
                            'avatar',
                            'direccion',
                            'comuna_id'
                        ]);

        return view('perfil.index', ["user" => $user]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'avatar'             => 'nullable|mimetypes:image/jpeg,image/png,image/gif,image/bmp|max:2048',
            ]);

            /** @var \App\User $user */
            $user = Auth::user();

            // Procesa avartar y si todo OK incluso guarda su nombre en el perfil del usuario
            if ($request->hasFile('avatar')) {
                $resultado = $this->procesarAvatar($request->file('avatar'), $user);

                if (!$resultado['success']) {
                    return redirect()->back()->withErrors(['avatar' => __('validation.avatar.processing_error', ['message' => $resultado['message']])]);
                }
            }

            return redirect('perfil')->with('status', __('auth.profile_updated_successfully'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al actualizar avatar de perfil', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', __('auth.profile_update_error'));
        }
    }

    public function edit($id)
    {
        $ide = Crypt::decrypt($id);

        $user = User::with([
                                'rol:id,nombre',
                                'empresa:id,razon_social',
                                'sucursal:id,nombre_sucursal',
                                'comuna:id,nombre,region_id',
                                'comuna.region:id,nombre'
                            ])
                        ->where('activo', 1)
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

        // El el imposible caso de que el usuario activo fuera a modificar otro perfil ¿!?
            // Cambiamos el uso de Gate por una comprobación directa de ID
            // if (Gate::denies('verificar-usuario', $user)) {
            //     abort(404);
            // }

            // Si el usuario activo no es el mismo que el usuario que se quiere editar -> ERROR 404 (feo)
            // if (Auth::id() !== $user->id) {
            //     abort(404);
            // }

        // Cambiamos el abort por un redirect con mensaje.
        // Si el usuario activo no es el mismo que el usuario que se quiere editar, redirigimos a panel con un mensaje de error
        if (Auth::id() !== $user->id) {
            return redirect()->route('panel')->with('error', __('auth.unauthorized_profile_edit'));
        }

        return view('perfil.modificar', [ 'user' => $user ]);
    }

    // $idx fue puesto solo por compatibilidad y activar mecanismos estándar RESTful de Laravel.
    // $idx es el ID del usuario que está modificando su perfil -> El usuario activo.
    // Por eso el método obtiene el id del usuario desde Auth. 
    public function update(Request $request, $idx )
    {
        try {
            /** @var \App\User $user */
            $user = Auth::user();
            $id = $user->id;

            $request->validate([
                'rut_usuario'        => 'required|string|max:12|unique:users,rut_usuario,' . $id,
                'nombre_usuario'     => 'required|string|max:255',
                'apellidos_usuario'  => 'required|string|max:255',
                'email'              => 'required|email|max:255|unique:users,email,' . $id,
                'telefono'           => 'required|string|max:30',
                'comuna_id'          => 'required|integer|exists:comunas,id',
                'direccion'          => 'required|string|max:255',
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

            $user->rut_usuario = $request->rut_usuario;
            $user->nombre_usuario = $request->nombre_usuario;
            $user->apellidos_usuario = $request->apellidos_usuario;
            $user->sucursal_id = $request->sucursal_id;
            $user->rol_id = $request->rol_id;
            $user->email = $request->email;
            $user->telefono = $request->telefono;
            $user->comuna_id = $request->comuna_id;
            $user->direccion = $request->direccion;
            $user->save();

            return redirect('perfil')->with('status', __('auth.profile_updated_successfully'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al actualizar datos del perfil', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', __('auth.profile_update_error'));
        }
    }

    /**
     * Show the form for changing the password.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function password($id) {
    	return view('perfil.password',['id' => $id]);
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => [
                    'required',
                    'string',
                    'min:8',             // must be at least 10 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'required_with:confirmPassword',
                    'same:confirmPassword'
                ],
                'confirmPassword' => [
                    'required',
                ]
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $usuario = Crypt::decrypt($request->user);
            $user = User::find($usuario);
            $user->password = bcrypt($request->password);
            $user->save();

            return redirect('perfil')->with('status', __('auth.password_updated_successfully'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al actualizar contraseña', [
                'user'  => $request->user,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', __('auth.password_update_error'));
        }
    }
}
