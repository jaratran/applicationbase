<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PanelController extends Controller
{
    /**
     * Muestra la página inicial de los usuarios autenticados.
     */
    public function index(): View
    {
        return view('panel');
    }
}
