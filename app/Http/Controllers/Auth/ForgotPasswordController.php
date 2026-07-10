<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            // 1) Validaciones generales: “¿está el email ingresado? ¿es un email válido?”
            $request->validate([
                'email' => 'required|email'
            ]);

            // 2) Validaciones propias: “¿existe usuario? ¿está activo? ¿está verificado?”
            $user = User::where('email', $request->email)
                        ->where('activo', 1)
                        ->first();

            if (! $user) {
                return back()
                    ->withInput()
                    ->with('error', __('auth.email_not_found')); // Mensaje definido en resources/lang/es/auth.php                
            }
            if (! $user->hasVerifiedEmail()) {
                return redirect()
                    ->route('login')
                    ->with('warning', __('auth.account_not_verified'));
            }

            // 3) Si todo OK, envío manual del reset link mediante el broker de contraseñas
            $response = $this->broker()->sendResetLink(
                $request->only('email')
            );

            // 4) Si el mail se envió correctamente, redirijo a login con el mensaje de estado
            if ($response === Password::RESET_LINK_SENT) {
                return redirect()
                    ->route('login')
                    ->with('status', __('auth.reset_link_sent'));
            }

            // 5) En caso de fallo, vuelvo a la vista con el error correspondiente
            return back()
                ->withInput($request->only('email'))
                ->with('error', __('auth.reset_link_failed'));

        } catch (\Throwable $e) {
            Log::error('❌ Error al enviar enlace de recuperación', [
                'email' => $request->email ?? null,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->with('error', __('auth.reset_link_error'));
        }
    }
}
