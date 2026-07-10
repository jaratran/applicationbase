<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validación de los campos de login (si quieres personalizarla aún más).
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|email',
            'password' => 'required|string',
        ]);
    }

    /**
     * Criterios personalizados para autenticación:
     * - Usuario debe estar activo
     */
    protected function credentials(Request $request)
    {
        return [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'activo' => 1, // Aseguramos que el usuario esté activo
        ];
    }

    /**
     *  Aplicar filtro tipo whereNotNull sobre el campo email_verified_at
     *  para que solo se autentiquen usuarios que hayan verificado su correo.
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        $user = \App\Models\User::where('email', $credentials['email'])
                                ->where('activo', 1) // Aseguramos que el usuario esté activo
                                ->first();

        if (! $user || is_null($user->email_verified_at)) {
            return false;
        }

        return Auth::attempt($credentials, $request->filled('remember'));
    }

    /**
     * Redefine el mensaje de error cuando las credenciales no coinciden.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $email = $request->get('email');

        $user = \App\Models\User::where('email', $email)->first();

        if ($user) {
            if (! $user->activo) {
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => __('auth.account_inactive'),
                    ]);
            }

            if (is_null($user->email_verified_at)) {
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => __('auth.account_not_verified'),
                    ]);
            }
        }

        // Caso genérico: usuario no encontrado o clave incorrecta
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => __('auth.failed'),
            ]);
    }
}
