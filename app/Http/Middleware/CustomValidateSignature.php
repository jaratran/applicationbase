<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Lang;

class CustomValidateSignature
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')
                ->with('error', __('auth.verification_link_invalid'));  // Mensaje definido en resources/lang/es/auth.php
        }

        return $next($request);
    }
}