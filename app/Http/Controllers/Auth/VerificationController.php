<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Providers\RouteServiceProvider;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Notifications\CustomVerifyWelcomeEmail;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
        $this->middleware('auth')->except('verify');
        $this->middleware('signed_custom')->only('verify'); // Validación personalizada del enlace firmado que redirige al login si la firma no es válida
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, $id, $hash)
    {
        try {
            $user = User::findOrFail($id);

            if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                return redirect()->route('login')->with('error', __('auth.verification_link_invalid')); // Mensaje definido en resources/lang/es/auth.php
            }
        
            if ($user->hasVerifiedEmail()) {
                return redirect()->route('login')->with('warning', __('auth.account_already_verified')); // Mensaje definido en resources/lang/es/auth.php
            }
        
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
        
            $token = Crypt::encrypt($user->email);
            return redirect()->route('user.password', ['token' => $token]);

        } catch (\Throwable $e) {
            Log::error('❌ Error al verificar correo electrónico', [
                'id'    => $id ?? null,
                'email' => $request->email ?? null,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('error', __('auth.verification_error'));
        }
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? response()->noContent()
                : redirect($this->redirectPath());
        }

        if (!$user->activo) {
            return $request->wantsJson()
                ? response()->json(['message' => __('auth.activation_email_unavailable')], 422)
                : back()->with('error', __('auth.activation_email_unavailable'));
        }

        $user->notify(new CustomVerifyWelcomeEmail($user));

        return $request->wantsJson()
            ? response()->json([], 202)
            : back()->with('resent', true);
    }
}
