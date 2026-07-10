@extends('layouts.app')

@section('head-scripts')
    <!-- Select2 CSS (mejora visual y funcional de elementos <select>) -->
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap.css') }}">
    <!-- Personalizar select2 en cuanto a altura de selector y color de opción destacada -->
    <link rel="stylesheet" href="{{ asset('css/personalizaciones-select2.css') }}">

    <!-- CSS para el 'x' de las opciones del select2 de Patente de Camión -->
    <style>
        /* 1) Convirtiendo el contenedor en flex para distribuir espacio */
        #patente_camion + .select2-container .select2-selection--single {
            display: flex !important;
            align-items: center !important;
            padding-right: 0.5em !important; /* espacio al padre */
        }

        /* 2) Dejando que el texto ocupe todo el espacio disponible */
        #patente_camion + .select2-container .select2-selection--single .select2-selection__rendered {
            flex: 1 1 auto !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 3) Empujando el botón de borrar al extremo derecho */
        #patente_camion + .select2-container .select2-selection--single .select2-selection__clear {
            margin-left: auto !important;
            order: 1 !important;
        }

        /* 4) Ajustando la flecha desplegable para que no se solape */
        #patente_camion + .select2-container .select2-selection--single .select2-selection__arrow {
            margin-left: 0.25em !important;
        }
    </style>

    <!-- Flatpickr CSS: selector visual de hora y minuto en modo 24h y sin calendario -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Flatpickr JS: selector visual de hora y minuto en modo 24h y sin calendario -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

<!-- Parámetros de contexto de ejecución de vista -->
@php
    $esCreate        = true;      // Control para saber si estamos en creación (y no edición)
    $esPlanificacion = true;       // Control para saber si estamos en planificación  de retiros o solicitud de retiros
    $esEdit          = !$esCreate; // Estamos en edición (no creación)
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white fs-5">
                        Crear Retiro de Materia Prima
                    </div>
                    <form id="formSolicitud" action="{{ route('planificaciones-retiro.store-manual') }}" method="POST">
                        @csrf

                        <div class="card-body">
                            @include('includes.alertas-sistema')

							<!-- CONTEXTO GLOBAL DE LA VISTA (persistencia en rebote) -->
							<!-- Se prioriza rebote y si no hay se asume Región X -->
							<input type="hidden" name="region_operativa" id="region_operativa" value="{{ old('region_operativa', config('constantes.X_Region')) }}">

							<!--
							<script>
								console.log('En el inicio del card-body del Body');
								console.log('☑️ region_operativa : ', "{{  old('region_operativa', config('constantes.X_Region')) }}");
							</script>
							-->

                            <div class="bg-light border rounded p-3 mb-4">
                                <h5>Datos del Solicitante</h5>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Nombre Usuario</label>
                                        <input type="text" class="form-control"
                                            value="{{ $usuario->nombre_usuario }}"
                                            disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Apellidos Usuario</label>
                                        <input type="text" class="form-control"
                                            value="{{ $usuario->apellidos_usuario }}"
                                            disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Rol de Usuario</label>
                                        <input type="text" class="form-control"
                                            value="{{ $usuario->rol->nombre }}"
                                            disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-light border rounded p-3 mb-4">
                                <h5>Datos de la Solicitud de Retiro</h5>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Fecha de la Solicitud</label>
                                        <input type="text" class="form-control" value="{{ now()->format('Y-m-d') }}" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                    </div>
                                    <div class="form-group col-md-4">
										<div id="alerta-region-xii"
											class="alert alert-warning mt-2 d-none"
											role="alert">
											<i class="fas fa-exclamation-triangle me-1"></i>
											Está seleccionando una sucursal de la <strong>Región XII</strong>.
										</div>
									</div>
                                </div>

                                <div class="row my-3">
                                @switch($usuario->rol_id)
                                    @case(config('constantes.ROL_SOLICITANTE_PLANTA'))
                                        <div class="form-group col-md-4">
                                            <label for="sucursal_retiro">Planta de Proceso</label>
                                            <input type="text" class="form-control" value="{{ $sucursal?->nombre_sucursal }}" disabled>
                                            <!-- Campo oculto para mantener la sucursal del usuario -->
                                            <input type="hidden" name="sucursal_retiro" id="sucursal_retiro_hidden" value="{{ $sucursal?->id }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Productora de Materia Prima</label>
                                            <select class="form-control select2" id="empresa_retiro" name="empresa_retiro" required>
                                                <option value="">Cargando Empresas ...</option>
                                            </select>
                                            <!-- Campo oculto para almacenar la empresa preingresadas en caso de volver desde el back con error  -->
                                            <input type="hidden" id="empresa_actual" value="{{ old('empresa_retiro') }}">
                                        </div>
                                        @break

                                    @case(config('constantes.ROL_SOLICITANTE_PRODUCTOR'))
                                        <div class="form-group col-md-4">
                                            <label for="empresa_retiro">Productora de Materia Prima</label>
                                            <input type="text" class="form-control" value="{{ $empresa?->razon_social }}" disabled>
                                            <!-- Campo oculto para mantener la empresa del usuario -->
                                            <input type="hidden" name="empresa_retiro" id="empresa_retiro_hidden" value="{{ $empresa?->id }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Planta de Proceso</label>
                                            <select class="form-control select2" id="sucursal_retiro" name="sucursal_retiro" required>
                                                <option value="">Cargando Sucursales ...</option>
                                            </select>
                                            <!-- Campo ocultos para almacenar la sucursal preingresadas en caso de volver desde el back con error  -->
                                            <input type="hidden" id="sucursal_actual" value="{{ old('sucursal_retiro') }}">
                                        </div>
                                        @break

                                    @default
                                        {{-- Rol del usuario no coincide con solicitante en ningún caso --}}
                                        <div class="form-group col-md-4">
                                            <label>Productora de Materia Prima</label>
                                            <select class="form-control select2" id="empresa_retiro" name="empresa_retiro" required>
                                                <option value="">Cargando Empresas ...</option>
                                            </select>
                                            <!-- Campo oculto para mantener la empresa del usuario y enviarla al back en caso de que quede disbled -->
                                            <input type="hidden" name="empresa_retiro" id="empresa_retiro_hidden" value="{{ old('empresa_retiro') }}">
                                            <!-- Campo oculto para almacenar la empresa preingresadas en caso de volver desde el back con error  -->
                                            <input type="hidden" id="empresa_actual" value="{{ old('empresa_retiro') }}">
                                        </div>

                                        <div class="col-md-4 d-flex align-items-end" id="zona-boton">
                                            <button type="button" id="btn-fijar" class="btn d-none"></button>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label>Planta de Proceso</label>
                                            <select class="form-control select2" id="sucursal_retiro" name="sucursal_retiro" required>
                                                <option value="">Cargando Sucursales ...</option>
                                            </select>
											<!-- Campo oculto para mantener la sucursal del usuario y enviarla al back en caso de que quede disbled -->
                                            <input type="hidden" name="sucursal_retiro" id="sucursal_retiro_hidden" value="{{ old('sucursal_retiro') }}">
                                            <!-- Campo ocultos para almacenar la sucursal preingresadas en caso de volver desde el back con error  -->
                                            <input type="hidden" id="sucursal_actual" value="{{ old('sucursal_retiro') }}">
                                        </div>
                                @endswitch
                                </div>
                            </div>

                            <!-- Contenedor de retiros -->
                            <div id="retiros-container">
                                @php
                                    $retiros = old('fecha_retiro') ?? [null]; // Siempre mostrar al menos uno
                                @endphp

                                @foreach($retiros as $index => $valor)
                                    {{--
                                    <script>
                                        console.log('En el ForEach del Body');
                                        console.log('☑️ requiere[{{ $index }}]:', "{{ old('requiere_reposicion_hidden')[$index] ?? 'NO EXISTE' }}");
                                        console.log('📦 cantidad_bins[{{ $index }}]:', "{{ old('cantidad_bins_hidden')[$index] ?? 'NO EXISTE' }}");
                                    </script>
                                    --}}

                                    <div class="retiro-item bg-light border rounded p-3 mb-4" data-index="{{ $index }}">
										@include('includes.partial-retiro-wrapper', [
											'index'             => $index,
											'retiro'            => null
										])
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-success btn-sm mb-2" id="btnAddRetiro">
                                <i class="fa fa-plus"></i> Agregar Retiro
                            </button>
                        </div>

                        <div class="card-footer">
                            <button type="submit" id="btnCrearSolicitud" class="btn btn-primary my-2">
                                <i class="fa fa-check"></i> Crear Solicitud
                            </button>
                            <button type="button" class="btn btn-secondary my-2" onclick="window.location.href='{{ route('planificaciones-retiro.index') }}'">
                                <i class="fa fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>

                    <!-- Rretiros Template para Clonado y agregar más Retiros -->
                    <div class="retiro-item-template bg-light border rounded p-3 mb-4 d-none" data-index="__INDEX__">
						@include('includes.partial-retiro-wrapper', [
							'index'           => '__INDEX__',
							'retiro'          => null
						])
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación de retiro -->
    <div class="modal fade" id="confirmDeleteRetiroModal" tabindex="-1" aria-labelledby="confirmDeleteRetiroLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteRetiroForm">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="confirmDeleteRetiroLabel">Eliminar Retiro</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 4rem;"></i>
                        <p class="fs-5">¿Está seguro que desea eliminar este retiro?</p>

                        <div class="form-group w-100">
                            <p class="text-muted small mb-3">Este cambio no se puede deshacer.</p>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                        <button type="button" id="btnConfirmDeleteRetiro" class="btn btn-danger">
                            <i class="fa fa-trash"></i> Eliminar Retiro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('endbody-scripts')
    <!-- Select2 JavaScript (extensión de elementos <select> con búsqueda, multiselección, etc.) -->
    <script src="{{ asset('js/select2.full.js') }}"></script>
    @include('includes.constantes-js-catalogo')

    <!-- Seteo inicial de Bootstrap en Select2 -->
    <script>
        window.onload = function() {
            $(".select2").select2({theme: "bootstrap"});
        }
    </script>

    <!-- Manejo de Select2 de Productoras y Plantas para usuarios AdminIT y Coordinador -->
    <!-- Inyectamos un objeto JS global en el Blade con valores requeridos por los JS -->
    <script>
        window.contextUsuario = {
            rol_id: {{ $usuario->rol_id }},
            empresa_id: {!! json_encode($usuario->empresa_id) !!},
            sucursal_id: {!! json_encode($usuario->sucursal_id) !!}
        };
    </script>
    <script src="{{ asset('js/retiro-interacciones-plantas-productoras.js') }}"></script>

	<!-- Inyectamos objeto JS global para saber cuando es la primera carga, cuando lo consultemos lo seteamos en false -->
	<script>
		window.listenerTipoTransporte = false;

		// console.log('📦 En el bloque JS de recien cargado');
		// console.log('listenerTipoTransporte:', window.listenerTipoTransporte);
	</script>

	<!-- Inyectamos objeto JS global para saber el estado de la Solicitud, porque si es PROGRAMADA los elementos de fecha deben estar disabled -->
	<script>
		window.estadoSolicitud = {{ $retiro->estado_id ?? 'null' }};

		// console.log('📦 En el bloque JS de recien cargado');
		// console.log('estadoSolicitud:', window.estadoSolicitud);
	</script>

	<!-- Inyectamos objeto JS global para manejar parámetros operacionales de demoras por transporte maritimo/terrestre desde la Región XII a Región X -->
	<script>
		window.operationalParameters = {
			maritimeTransitDurationDays:    {{ (int) $operationalParameter->maritime_transit_duration_days }},
			terrestrialTransitDurationDays: {{ (int) $operationalParameter->terrestrial_transit_duration_days }},
			combinedTransitDurationDays:    {{ (int) $operationalParameter->combined_transit_duration_days }},
			delayArriboEtaHours:            {{ (int) $operationalParameter->delay_arribo_eta_hours }},
		};
	</script>

    <!-- Inyectamos objeto JS global para manejo del contexto de región y despliegue correcto de parámetros del retiro -->
	<script>
		const regionesPermitidas = @json(auth()->user()->regiones_operativas_ids);

		if (regionesPermitidas.includes(REGION_X)) {																	// Si incluye Region X comenzamos ofreciendo esa
			window.regionOperativa = Number(@json(	old('region_operativa', config('constantes.REGION_X'))		));		// Constante necesaria porque en el primer render el old es null
		}
		else{																											// Si NO incluye X es de la XII, comenzamos con esa
			window.regionOperativa = Number(@json(	old('region_operativa', config('constantes.REGION_XII'))	));		// Constante necesaria porque en el primer render el old es null
		}

		// console.log('📦 En el bloque JS de rescate de region operativa de rebote por error en backend');
        // console.log('regionOperativa:', window.regionOperativa);
    </script>
	<!-- Función que activa la estructura válida del Partial Blade : Región X o Región XII -->
	<script src="{{ asset('js/retiro-region-operativa-wrapper.js') }}"></script>

    <!-- Sincronización de constantes JS con parámetros de contexto de ejecución de vista  -->
    <script>
        window.contextoVista = {
            esCreate:        {{ $esCreate ? 'true' : 'false' }},
            esPlanificacion: {{ $esPlanificacion ? 'true' : 'false' }},
            esEdit:          {{ $esEdit ? 'true' : 'false' }}
        };

        // console.log('📦 En el bloque JS de rescate de sicronización de constantes JS con parámetros de contexto de ejecución de vista');
        // console.log('esCreate:',        window.contextoVista.esCreate);
        // console.log('esPlanificacion:', window.contextoVista.esPlanificacion);
        // console.log('esEdit:',          window.contextoVista.esEdit);
    </script>

    <!-- JS para coordinar campos tipo_retiro, requiere_reposicion y cantidad_bins en el formulario de Solicitud de Retiro -->
    <script src="{{ asset('js/solicitud-retiro-form-controls.js') }}"></script>

    <!-- Sincronización de Hidden antes de hacer Submit y deshabilitación del botón Submit inmediatamente tras el primer envío. -->
    <!-- Sincronización Hidden antes del Submit y Carga de arreglos OLDs por inicio o por rebote -->
    <!-- Y carga de arreglos OLDs por inicio o por rebote -->
    <script>
        // Rescate de valores OLD al llegar por rebote de error en el Back. Si no es rebote todos quedan en null array vacío.
        window.oldValues = {
            kilogramos_estimados_hidden: @json(old('kilogramos_estimados_hidden')),     // Este es necesario para pintar blade
            tipo_retiro: @json(old('tipo_retiro')),                                     // Este es necesario para el toggleTipoRetiro
            requiere_reposicion_hidden: @json(old('requiere_reposicion_hidden')),       // Este es necesario para pintar blade
            cantidad_bins_hidden: @json(old('cantidad_bins_hidden')),                   // Este es necesario para pintar blade
            tipo_operacion_hidden: @json(old('tipo_operacion_hidden')),                 // Este es necesario para pintar blade
            tiene_restriccion_hidden: @json(old('tiene_restriccion_hidden'))            // Este es necesario para pintar blade
		};

        // console.log('📦 En el bloque JS de rescate de arreglos de rebote por error en backend');
        // console.log('tipo_retiro:', window.oldValues.tipo_retiro);
        // console.log('requiere_reposicion_hidden:', window.oldValues.requiere_reposicion_hidden);
        // console.log('cantidad_bins_hidden:', window.oldValues.cantidad_bins_hidden);
    </script>
    <script src="{{ asset('js/retiros-sync-hidden-catch-olds.js') }}"></script>

    <!-- Agregar/Eliminar bloques de Retiro-Planificación y actualizar indices de todos los form-control -->
    <script src="{{ asset('js/retiros-multiples.js') }}"></script>

    <!-- JS para coordinar campos Horas Planificada/Llegada, Tranportistas y Conductores según Camión en el formulario de Solicitud de Retiro -->
    <script src="{{ asset('js/planificacion-retiro-form-controls.js') }}"></script>

    <!-- Validación dinámica de la fecha_planificacíon para que siempre sea mayor al máximo entre HOY y la fecha que se está solicitando 'MANUALMENTE' para el retiro -->
    <script>
        document.addEventListener('input', function (event) {
            if (event.target.classList.contains('fecha_retiro')) {
                const inputRetiro = event.target;
                const idx = inputRetiro.id.split('_').pop(); // extrae el index desde id="fecha_retiro_{{ $index }}"
                const valorRetiro = inputRetiro.value;

                if (valorRetiro) {
                    const inputPlan = document.getElementById(`fecha_planificada_${idx}`);
                    if (inputPlan) {
                        // El min será la mayor entre hoy y la fecha de retiro
                        const hoy = new Date();
                        const retiro = new Date(valorRetiro);

                        // comparar y tomar el mayor
                        const minDate = retiro > hoy ? retiro : hoy;

                        // Formatear a YYYY-MM-DDTHH:MM
                        const pad = (n) => n.toString().padStart(2, '0');
                        const formatted =
                            minDate.getFullYear() + '-' +
                            pad(minDate.getMonth() + 1) + '-' +
                            pad(minDate.getDate()) + 'T' +
                            pad(minDate.getHours()) + ':' +
                            pad(minDate.getMinutes());

                        inputPlan.min = formatted;
                    }
                }
            }
        });
    </script>
@endsection
