<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Luego de reestablecer contraseña, redirigir al login obligatoriamente.
     */
    protected function redirectTo()
    {
        return '/login';
    }

    /**
     * Solo usuarios no autenticados pueden acceder.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Muestra el formulario para establecer nueva contraseña.
     * 
     * Este método es personalizado para recuperar el correo desde la tabla
     * `password_resets` y pasarlo como campo oculto al Blade.
     *
     * @param  string  $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showResetForm(Request $request, $token = null)
    {
	    try {
	        $registro = DB::table('password_resets')->where('email', $request->email)->first();
	    
	        if (!$registro || !Hash::check($token, $registro->token)) {
	            return redirect()->route('login')->with('error', __('auth.reset_link_invalid')); // Mensaje definido en resources/lang/es/auth.php
	        }
	    
	        return view('auth.passwords.reset')->with([
	            'token' => $token,
	            'email' => $request->email,
	        ]);

	    } catch (\Throwable $e) {
	        Log::error('❌ Error al mostrar formulario de restablecimiento de contraseña', [
	            'email' => $request->email ?? null,
	            'error' => $e->getMessage(),
	            'line'  => $e->getLine(),
	            'trace' => $e->getTraceAsString(),
	        ]);

	        return redirect()->route('login')->with('error', __('auth.reset_form_error'));
	    }
    }

    /**
     * Puedes sobreescribir las reglas de validación si deseas aplicar requisitos más estrictos.
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',   // una minúscula
                'regex:/[A-Z]/',   // una mayúscula
                'regex:/[0-9]/',   // un número
                'confirmed'
            ],
        ];
    }

    /**
     * Sobrescribe el comportamiento por defecto para evitar login automático.
     *
     * @param  \App\User  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // No se ejecuta Auth::login($user)
    }
}
