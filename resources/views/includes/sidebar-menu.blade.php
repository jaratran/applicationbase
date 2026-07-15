<div id="sidebar">
    <ul class="nav flex-column">

        <li class="nav-item">
            <a href="{{ route('panel.index') }}"
                class="nav-link {{ Route::is('panel.index') ? 'active' : '' }}"
                {{ Route::is('panel.index') ? 'aria-current=page' : '' }}>
                <i class="fas fa-tachometer-alt me-2 text-secondary"></i>Panel de Control
            </a>
        </li>

        <!-- PARAMETROS GENERALES -->
        <!-- Esto es sólo para Admin-IT -->
        @if( in_array(Auth::user()->rol_id, [ config('constantes.ROL_ADMINISTRADOR_IT') ]) )
            <li class="nav-item">
                <a href="{{ route('parameters.index') }}"
                    class="nav-link {{ Route::is('parameters.index') ? 'active' : '' }}"
                    {{ Route::is('parameters.index') ? 'aria-current="page"' : '' }} >
                    <i class="fas fa-cogs me-2 text-secondary"></i>Parámetros Generales
                </a>
            </li>
        @endif

        <!-- MANTENEDORES DE ACTORES -->
        <!-- Esto es sólo para Admin-IT y Coordinadores X -->
        @if( in_array(Auth::user()->rol_id, [ config('constantes.ROL_COORDINADOR'), config('constantes.ROL_ADMINISTRADOR_IT') ]) )
            <li class="nav-item {{ Route::is('usuario.index') || Route::is('sucursal.index') || Route::is('empresa.index') ? 'active' : '' }}">
                <div class="nav-link fw-bold">
                    <i class="fa fa-list"></i> Gestión
                </div>
                <ul class="sidebar-submenu nav flex-column ms-3 ps-3 list-unstyled">

					<!-- Usuarios, Empresas y Sucursales comparten esta restricción de roles. -->
						<li>
							<a href="{{ route('usuario.index') }}"
							class="nav-link {{ Route::is('usuario.index') ? 'active' : '' }}"
							{{ Route::is('usuario.index') ? 'aria-current=page' : '' }}>
							<i class="fas fa-user me-2 text-secondary"></i>Usuarios
							</a>
						</li>
						<li>
							<a href="{{ route('empresa.index') }}"
							class="nav-link {{ Route::is('empresa.index') ? 'active' : '' }}"
							{{ Route::is('empresa.index') ? 'aria-current=page' : '' }}>
							<i class="fas fa-industry me-2 text-secondary"></i>Empresas
							</a>
						</li>
						<li>
							<a href="{{ route('sucursal.index') }}"
							class="nav-link {{ Route::is('sucursal.index') ? 'active' : '' }}"
							{{ Route::is('sucursal.index') ? 'aria-current=page' : '' }}>
							<i class="fas fa-building me-2 text-secondary"></i>Sucursales
							</a>
						</li>
                </ul>
            </li>
        @endif

    </ul>
</div>
