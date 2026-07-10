<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Muestra la página inicial según estado de autenticación.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function index()
    {
        if (!Auth::check()) {                     // Si NO está autenticado, redirige a ...
            return redirect('login');            //    Usuario NO autenticado -> Login para ingreso de usuario y contraseña
        } else {                                  // De lo contrario, usuario SI está autenticado
            return redirect('panel');            //    Usuario autenticado -> Se va al Panel
        }
    }

}
