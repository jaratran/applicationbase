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
        <!-- Esto es sólo para Admin-IT, Coodinadores y Coordinadores XII -->
        @if( in_array(Auth::user()->rol_id, [ config('constantes.ROL_COORDINADOR'), config('constantes.ROL_COORDINADOR_XII'), config('constantes.ROL_ADMINISTRADOR_IT') ]) )
            <li class="nav-item {{ Route::is('usuario.index') || Route::is('sucursal.index') || Route::is('empresa.index') || Route::is('conductor.index') || Route::is('camion.index') || Route::is('rampla.index') ? 'active' : '' }}">
                <div class="nav-link fw-bold">
                    <i class="fa fa-list"></i> Gestión
                </div>
                <ul class="sidebar-submenu nav flex-column ms-3 ps-3 list-unstyled">

					<!-- PERO esto es sólo para los 2 roles originales: Admin-IT y Coodinadores -->
					@if( in_array(Auth::user()->rol_id, [ config('constantes.ROL_COORDINADOR'), config('constantes.ROL_ADMINISTRADOR_IT') ]) )
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
					@endif

			        <!-- Esto si es para los 3 roles : Admin-IT, Coodinadores y Coordinadores XII -->
                    <li>
                        <a href="{{ route('conductor.index') }}"
                           class="nav-link {{ Route::is('conductor.index') ? 'active' : '' }}"
                           {{ Route::is('conductor.index') ? 'aria-current=page' : '' }}>
                           <i class="fas fa-id-badge me-2 text-secondary"></i>Conductores
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('camion.index') }}"
                           class="nav-link {{ Route::is('camion.index') ? 'active' : '' }}"
                           {{ Route::is('camion.index') ? 'aria-current=page' : '' }}>
                           <i class="fas fa-truck me-2 text-secondary"></i>Camiones
                        </a>
                    </li>
					<li>
						<a href="{{ route('rampla.index') }}"
						class="nav-link {{ Route::is('rampla.index') ? 'active' : '' }}"
						{{ Route::is('rampla.index') ? 'aria-current=page' : '' }}>
							<i class="fas fa-trailer me-2 text-secondary"></i>Ramplas (Región XII)
						</a>
					</li>
                </ul>
            </li>
        @endif

        <!-- MANTENEDORES DE SOLICITUDES DE RETIROS -->
        <li class="nav-item {{ Route::is('solicitudes-retiro.index') || Route::is('solicitudes-retiro.list') ? 'active' : '' }}">
            <div class="nav-link fw-bold">
                <i class="fa fa-cube"></i> Solicitudes de Retiro
            </div>

            <ul class="sidebar-submenu nav flex-column ms-3 ps-3 list-unstyled">
                @if( in_array(Auth::user()->rol_id, [   config('constantes.ROL_SOLICITANTE_PLANTA'),
				                                        config('constantes.ROL_SOLICITANTE_PLANTA_XII'),
				                                        config('constantes.ROL_SOLICITANTE_PRODUCTOR'),
                                                        // config('constantes.ROL_COORDINADOR'), // 15-07-25: Fue eliminado el permiso de los coordinadores para crea solicitudes. Para eso tienen la Manual.
                                                        config('constantes.ROL_ADMINISTRADOR_IT') ]) )
                    <li>
                        <a href="{{ route('solicitudes-retiro.create') }}"
                        class="nav-link {{ Route::is('solicitudes-retiro.create') ? 'active' : '' }}" {{ Route::is('solicitudes-retiro.create') ? 'aria-current=page' : '' }}>
                        <i class="fas fa-plus-square me-2 text-secondary"></i>Crear Solicitud de Retiro
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('solicitudes-retiro.index') }}"
                    class="nav-link {{ Route::is('solicitudes-retiro.index') ? 'active' : '' }}" {{ Route::is('solicitudes-retiro.index') ? 'aria-current=page' : '' }}>
                    <i class="fas fa-box me-2 text-secondary"></i>Ver Solicitudes de Retiro
                    </a>
                </li>
            </ul>
        </li>

        <!-- MANTENEDORES DE PLANIFICACIONES DE RETIROS -->
        <li class="nav-item  {{ Route::is('planificaciones-retiro.index') || Route::is('planificaciones-retiro.list') ? 'active' : '' }}">
            <div class="nav-link fw-bold">
                <i class="fa fa-tasks"></i> Planificaciones de Retiro
            </div>

            <ul class="sidebar-submenu nav flex-column ms-3 ps-3 list-unstyled">
                @if( in_array(Auth::user()->rol_id, [ config('constantes.ROL_COORDINADOR_XII'), config('constantes.ROL_COORDINADOR'), config('constantes.ROL_ADMINISTRADOR_IT') ]) )
                    <li>
                        <a href="{{ route('planificaciones-retiro.create-manual') }}"
                        class="nav-link {{ Route::is('planificaciones-retiro.create-manual') ? 'active' : '' }}"
                        {{ Route::is('planificaciones-retiro.create-manual') ? 'aria-current=page' : '' }}>
                        <i class="fas fa-edit me-2 text-secondary"></i>Crear Planificación Manual
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('planificaciones-retiro.index') }}"
                        class="nav-link {{ Route::is('planificaciones-retiro.index') ? 'active' : '' }}"
                        {{ Route::is('planificaciones-retiro.index') ? 'aria-current=page' : '' }}>
                        <i class="fas fa-calendar-check me-2 text-secondary"></i>Ver Planificaciones de Retiro
                    </a>
                </li>
            </ul>
        </li>

        <!-- PROGRAMAS DIARIOS -->
        <!-- Esto es para Admin-IT, Coodinadores y roles internos -->
        @if( in_array( Auth::user()->rol_id, [  config('constantes.ROL_COORDINADOR'),       config('constantes.ROL_ADMINISTRADOR_IT'),      // Admin-IT y Coordinadores
                                                config('constantes.ROL_PERSONAL_GERENCIA'), config('constantes.ROL_PERSONAL_PRODUCCION'),   // Roles Internos
                                                config('constantes.ROL_PERSONAL_CALIDAD'),  config('constantes.ROL_PERSONAL_MANTENCION'),
                                                config('constantes.ROL_PERSONAL_ROMANA') ] ))

            <li class="nav-item">
                <div class="nav-link fw-bold">
                    <i class="fa fa-cube"></i> Programas Diarios
                </div>
                <ul class="sidebar-submenu nav flex-column ms-3 ps-3 list-unstyled">

                    @if( in_array(Auth::user()->rol_id, [ config('constantes.ROL_COORDINADOR'), config('constantes.ROL_ADMINISTRADOR_IT') ]) )
                        <li>
                            <a href="{{ route('programa-diario.preparar-emision') }}"
                            class="nav-link {{ Route::is('programa-diario.preparar-emision') ? 'active' : '' }}"
                            {{ Route::is('programa-diario.preparar-emision') ? 'aria-current=page' : '' }}>
                            <i class="fas fa-file-alt me-2 text-secondary"></i>Emitir Programa Diario
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('programa-diario.index') }}"
                        class="nav-link {{ Route::is('programa-diario.index') ? 'active' : '' }}"
                        {{ Route::is('programa-diario.index') ? 'aria-current=page' : '' }}>
                        <i class="fas fa-history me-2 text-secondary"></i>Programas y Consolidados Anteriores
                        </a>
                    </li>
                </ul>
            </li>

        @endif
    </ul>
</div>
