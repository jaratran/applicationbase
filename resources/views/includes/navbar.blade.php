<nav class="navbar navbar-expand-lg bg-light px-3 sticky-top" style="height: 45px; z-index: 1030;">
    <div class="d-flex align-items-center">
        <!-- Botón Toggle -->
        <button class="btn btn-light p-2 me-2 align-self-center border-0" id="toggleSidebar" style="height: 40px;">
            <i class="text-dark fa fa-th fa-lg"></i>
        </button>

        <!-- Logo con enlace al Panel de Control -->
        <a href="{{ url('panel') }}" class="position-absolute top-0 start-0 mt-2 ms-5">
            <img src="{{ url('/config/'.$designParameter['logo_design']) }}" alt="Logo" style="max-height: 30px;">
        </a>
    </div>

    <!-- Avatar del usuario en la esquina superior derecha -->
    <div id="usermenu">
        <div class="position-absolute top-0 end-0 mt-2 me-3 dropdown">
            <a class="nav-link dropdown-toggle p-0 border-0 bg-transparent" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('uploads/avatar/' . (Auth::user()->avatar ? Auth::user()->avatar . '_small.jpg' : 'default_small.jpg')) }}"
                    alt="Avatar Pequeño"
                    class="rounded-circle"
                    style="width: 30px; height: 30px; object-fit: cover;">
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item {{ Route::is('perfil.index') ? 'active' : '' }}"
                        href="{{ route('perfil.index') }}"
                        {{ Route::is('perfil.index') ? 'aria-current=page' : '' }}>
                    <i class="fas fa-user-circle me-2 text-secondary"></i>Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                        href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2 text-secondary"></i>Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
