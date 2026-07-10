@extends('layouts.app')

@section('head-scripts')
    <!-- Select2 CSS (mejora visual y funcional de elementos <select>) -->
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap.css') }}">
    <!-- Personalizar select2 en cuanto a altura de selector y color de opción destacada -->
    <link rel="stylesheet" href="{{ asset('css/personalizaciones-select2.css') }}">

    <!-- CSS para renglones de comentarios -->
    <style>
        .comentario:nth-child(odd) {
            background-color: #ffffff;
        }
        .comentario:nth-child(even) {
            background-color: #e9ecef;
            border-radius: 0.25rem;
            padding: 0.75rem;
        }
    </style>
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
    $esCreate        = false;                                                             // Control para saber si estamos en creación (y no edición)
    $esPlanificacion = true;                                                              // Control para saber si estamos en planificación  de retiros o solicitud de retiros
    $esEdit          = !$esCreate;                                                        // Estamos en edición (no creación)
	$regOperativa    = $retiro->solicitud->region_operativa_id;                           // Para pasarle la región operativa al JS y defina la variable de inicio
@endphp

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white fs-5">
                    @if ($retiro->estado_id == config('constantes.ESTADO_RETIRO_ACEPTADO'))
                        Efectuar Planificación de Retiro
                    @else
                        Editar Planificación de Retiro
                    @endif
                </div>
                <form id="formSolicitud" action="{{ route('planificaciones-retiro.update', $retiro->planificacion->id) }}" method="POST" enctype="multipart/form-data">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}

                    <div class="card-body">
    					@include('includes.alertas-sistema')

                        {{-- Datos del Solicitante --}}
                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Datos del Solicitante</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Nombre Usuario</label>
                                    <input type="text" class="form-control"
                                        value="{{ $retiro->solicitud->usuario->nombre_usuario }}"
                                        disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Apellidos Usuario</label>
                                    <input type="text" class="form-control"
                                        value="{{ $retiro->solicitud->usuario->apellidos_usuario }}"
                                        disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Rol de Usuario</label>
                                    <input type="text" class="form-control"
                                        value="{{ $retiro->solicitud->usuario->rol->nombre }}"
                                        disabled>
                                </div>
                            </div>
                        </div>

                        {{-- Datos de la Solicitud del Retiro --}}
                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Datos del Retiro Solicitado</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>N° de Retiro</label>
                                    <input type="text" class="form-control"
                                        value="{{ $retiro->id }}"
                                        disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Fecha de la Solicitud</label>
                                    <input type="text" class="form-control"
                                        value="{{ optional($retiro->solicitud->created_at)->format('Y-m-d') }}"
                                        disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Estado del Retiro</label>
                                    <input type="text" class="form-control"
                                        value="{{ $retiro->estado->nombre }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="row my-3">
                                <div class="form-group col-md-4">
                                    <label>Planta</label>
                                    <input type="text" class="form-control"
                                        value="{{ $retiro->solicitud->maquila->sucursal->nombre_sucursal }}"
                                        disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Proveedor</label>
                                    <input type="text" class="form-control"
                                        value="{{ $retiro->solicitud->maquila->empresa->razon_social }}"
                                        disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Región Operativa</label>
                                    <input type="text" class="form-control"
                                        value="{{ $retiro->solicitud->region_operativa_codigo }}"
                                        disabled>
                                </div>
                            </div>
                        </div>

                        {{-- Datos de la Planificación del Retiro - Reutilizamos el partial item-retiro --}}
                        <div class="retiro-item bg-light border rounded p-3 mb-4" data-index="{{ 0 }}">
                            @include('includes.partial-retiro-wrapper', [
								'index' => 0,
								'retiro' => $retiro
							])
                        </div>

                        <input type="hidden" name="motivo_modificacion_final" id="motivo_modificacion_final" value="">
                    </div>

                    <div class="card-footer">
                        <button type="button" class="btn btn-primary my-2" id="btnActualizar" data-estado="{{ $retiro->estado_id }}">
                            @if ($retiro->estado_id == config('constantes.ESTADO_RETIRO_ACEPTADO'))
                                <i class="fa fa-calendar-alt"></i> Efectuar Planificación
                            @else
                                <i class="fa fa-edit"></i> Actualizar Planificación
                            @endif
                        </button>

                        <button type="button" class="btn btn-secondary my-2" onclick="window.location.href='{{ route('planificaciones-retiro.index') }}'">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar observación a solicitud -->
<div class="modal fade" id="agregaComentarioModal" tabindex="-1" aria-labelledby="motivoModificacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="motivoModificacionLabel">Seleccionar Motivo de Modificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center">
                <i class="fas fa-pen-alt text-warning mb-3" style="font-size: 4rem;"></i>

                <p class="fs-5">¿Está seguro que desea modificar esta planificación?</p>
                <p class="text-muted small mb-3">Debe seleccionar motivo.</p>

                <div class="d-flex justify-content-center">
                    <div class="form-group" style="min-width: 300px;">
                        <select name="motivo_modificacion"
                                id="motivo_modificacion"
                                class="form-control select2 mb-3"
                                required
                                data-placeholder="Seleccione motivo de modificación">
                            <option value="">Seleccione un motivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning text-dark" id="btnComentar" data-bs-dismiss="modal" disabled>
                    <i class="fa fa-check-circle"></i> Seleccionar Motivo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    <!-- Select2 JavaScript (extensión de elementos <select> con búsqueda, multiselección, etc.) -->
    <script src="{{ asset('js/select2.full.js') }}"></script>
    @include('includes.constantes-js-catalogo')

    <!-- Seteo inicial de Bootstrap en Select2 FUSIONADO con la inicialización de Select2 en modales -->
    <!-- El: Modal para Motivo de Modificación al momento de Editar Planificación -->
    <script>
        window.onload = function() {
            $('.select2').each(function () {
                const $modalParent = $(this).closest('.modal');

                $(this).select2({
                    theme: 'bootstrap',
                    dropdownParent: $modalParent.length ? $modalParent : $('body')
                });
            });
        };
    </script>

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
			maritimeTransitDurationDays: {{ (int) $operationalParameter->maritime_transit_duration_days }},
			terrestrialTransitDurationDays: {{ (int) $operationalParameter->terrestrial_transit_duration_days }},
			combinedTransitDurationDays: {{ (int) $operationalParameter->combined_transit_duration_days }},
			delayArriboEtaHours:            {{ (int) $operationalParameter->delay_arribo_eta_hours }},
		};
	</script>

    <!-- Inyectamos objeto JS global para manejo del contexto de región y despliegue correcto de parámetros del retiro -->
	<script>
		// Acá en EDIT rescatamos la región operativa de la solicitud lo cual lo recibimos al principio de este Blade en $regOperativa.
		window.regionOperativa = Number(@json( $regOperativa ));

        // console.log('📦 En el bloque JS de rescate de arreglos de rebote por error en backend');
		// console.log('☑️ $regOperativa   : ', '{{ $regOperativa }}');
        // console.log('☑️ regionOperativa : ', window.regionOperativa);
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

    <!-- JS para coordinar campos Horas Planificada/Llegada, Tranportistas y Conductores según Camión en el formulario de Solicitud de Retiro -->
    <script src="{{ asset('js/planificacion-retiro-form-controls.js') }}"></script>

    <!-- Sincronización Hidden antes del Submit y Carga de arreglos OLDs por inicio o por rebote -->
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

	<!-- Actualiza CHEVRONES para mostrar/ocultar comentarios del retiro y cambios del retiro -->
    <script src="{{ asset('js/retiro-expand-collapse-chevrons.js') }}"></script>

    <!-- Solicitud de selección de comentario al efectuar la actualización de la planificación -->
    <script>
        const agregaComentarioModal = new bootstrap.Modal(document.getElementById("agregaComentarioModal"));

        $(document).on("click", "#btnActualizar", function () {

            // Antes de continuar se fuerza la validación nativa HTML5 explícitamente
            const form = document.getElementById('formSolicitud');

            // Por si hay errores de validación → se muestra tooltips y NO abre modal
            if (!form.checkValidity()) {
                form.reportValidity();      // muestra los tooltips nativos
                return;
            }

            const estado = parseInt($(this).data('estado'));

            if (estado === ESTADO_RETIRO_ACEPTADO) { // Planificación directa
                $('#motivo_modificacion_final').val(0); // Estamos CREANDO no ACTUALIZANDO - No hay motivo de modificación
                $('#formSolicitud').submit();

            } else { // Mostrar modal de comentario
                document.getElementById('btnComentar').disabled = true;
                agregaComentarioModal.show();
            }
        });

        $('#motivo_modificacion').on('change', function () {
            const motivoId = $(this).val();

            if (motivoId) {
                document.getElementById('btnComentar').disabled = false;
             } else {
                document.getElementById('btnComentar').disabled = true;
            }
        });

        $(document).on("click", "#btnComentar", function () {
            const motivoId = $('#motivo_modificacion').val();
            $('#motivo_modificacion_final').val(motivoId);
            $('#formSolicitud').submit();
        });

        // Opcional: resetear select2 al cerrar la modal
        $('#agregaComentarioModal').on('hidden.bs.modal', function () {
            $('#motivo_modificacion').val('').trigger('change');
            $('#motivo_modificacion').select2('destroy');
        });


        switch (window.regionOperativa) {
			case REGION_X:
				catalogo2select2(CATEGORIA_CAMBIOS_PLANIFICACION, 'motivo_modificacion', 'Seleccione motivo de modificación');
                break;

            case REGION_XII:
				catalogo2select2(CATEGORIA_CAMBIOS_PLANIFICACION_XII, 'motivo_modificacion', 'Seleccione motivo de modificación');
                break;

			default:	// Si no es X o XII deja ambas ocultas
		}

    </script>
@endsection
