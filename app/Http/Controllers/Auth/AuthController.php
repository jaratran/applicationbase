<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\User;

class AuthController extends Controller
{
    /**
     * Cierra la sesión del usuario actual.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Muestra el formulario para definir la contraseña tras verificar email.
     */
    public function passwordUser($token)
    {
        try {
            $email = Crypt::decrypt($token);
        } catch (DecryptException $e) {
            Log::error('❌ Token inválido al intentar definir contraseña', [
                'token' => $token,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('warning', __('auth.token_invalid')); // Mensaje definido en resources/lang/es/auth.php
        }
    
        $user = User::where('email', $email)->first();
    
        if (! $user || ! $user->hasVerifiedEmail()) {
            Log::warning('⚠️ Usuario no verificado intentó acceder a definición de contraseña', [
                'email' => $email,
            ]);

            return redirect()->route('login')->with('error', __('auth.email_not_verified')); // Mensaje definido en resources/lang/es/auth.php
        }
    
        session()->flash('status', __('auth.set_password_instruction'));
        return view('auth.password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Guarda la contraseña definida por el usuario y activa la cuenta.
     */
    public function activateUser(Request $request)
    {
        try {
            // Validación de campos
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            // Desencriptar token
            $decryptedEmail = Crypt::decrypt($request->token);

            // Verificar que el token corresponde al email enviado
            if ($request->email !== $decryptedEmail) {
                return redirect()->route('login')->with('error', __('auth.email_token_mismatch')); // Mensaje definido en resources/lang/es/auth.php
            }
        
            // Buscar el usuario y validar que haya verificado su email
            $user = User::where('email', $request->email)->first();
            if (! $user || ! $user->hasVerifiedEmail()) {
                return redirect()->route('login')->with('error', __('auth.email_not_verified')); // Mensaje definido en resources/lang/es/auth.php
            }
        
            // Verificar que aún no tenga contraseña definida (opcional, por seguridad)
            if ($user->password) {
                return redirect()->route('login')->with('warning', __('auth.password_already_defined')); // Mensaje definido en resources/lang/es/auth.php
            }
        
            // Guardar la nueva contraseña cifrada
            $user->password = Hash::make($request->password);
            $user->save();
        
            // Redirigir al login con mensaje de éxito. Mensaje definido en resources/lang/es/auth.php
            return redirect()->route('login')->with('status', __('auth.password_set_success'));

        } catch (DecryptException $e) {
            Log::warning('⚠️ Error al desencriptar token en activateUser()', [
                'token' => $request->token,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('login')->with('error', __('auth.token_mismatch'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al activar usuario y definir contraseña', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('login')->with('error', __('auth.activation_error'));
        }
    }

    /**
     * Devuelve el guard por defecto de autenticación.
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
