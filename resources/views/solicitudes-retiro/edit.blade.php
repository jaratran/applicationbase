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

	<!-- CSS para destacar campos cambiados en el historial de cambios -->
	<style>
		.bg-cambio {
			background-color: rgba(var(--bs-warning-rgb), 0.15) !important;
		}
	</style>
@endsection

<!-- Parámetros de contexto de ejecución de vista -->
@php
    $esCreate        = false;       // Control para saber si estamos en creación (y no edición)
    $esEdit          = !$esCreate; // Estamos en edición (no creación)
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white fs-5">
                        Editar Retiro de Materia Prima
                    </div>

                    <form id="formSolicitud" action="{{ route('solicitudes-retiro.update', $retiro->id) }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}

                        <div class="card-body">
                            @include('includes.alertas-sistema')

                            {{-- Datos del Solicitante --}}
                            <div class="bg-light border rounded p-3 mb-4">
                                <h5>Datos del Solicitante</h5>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Nombre Usuario</label>
                                        <input type="text" class="form-control" value="{{ $retiro->solicitud->usuario?->nombre_usuario }}" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Apellidos Usuario</label>
                                        <input type="text" class="form-control" value="{{ $retiro->solicitud->usuario?->apellidos_usuario }}" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Rol de Usuario</label>
                                        <input type="text" class="form-control" value="{{ $retiro->solicitud->usuario?->rol?->nombre }}" disabled>
                                    </div>
                                </div>
                            </div>

                            {{-- Datos del Retiro Solicitado --}}
                            <div class="bg-light border rounded p-3 mb-4">
                                <h5>Datos del Retiro Solicitado</h5>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>N° de Retiro</label>
                                        <input type="text" class="form-control" value="{{ $retiro->id }}" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Fecha de la Solicitud</label>
                                        <input type="text" class="form-control" value="{{ $retiro->solicitud->created_at->format('Y-m-d') }}" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Estado del Retiro</label>
                                        <input type="text" class="form-control" value="{{ $retiro->estado?->nombre }}" disabled>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="form-group col-md-4">
                                        <label>Productora de Materia Prima</label>
                                        <input type="text" class="form-control" value="{{ $empresa?->razon_social }}" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Planta de Proceso</label>
                                        <input type="text" class="form-control" value="{{ $sucursal?->nombre_sucursal }}" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Región</label>
                                        <input type="text" class="form-control" value="{{ $retiro->solicitud->region_operativa_codigo }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalle del Retiro - Reutilizamos el partial item-retiro -->
                            <div class="retiro-item bg-light border rounded p-3 mb-4" data-index="0">
                                @include('includes.partial-retiro-wrapper', ['index' => 0, 'retiro' => $retiro])

                                <!-- Historial de Comentarios -->
                                @if ($retiro->comentarios->isNotEmpty())
                                    <div class="row my-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-0">Historial de Comentarios</h5>
                                            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#comentariosCollapse"
                                                    aria-expanded="false" aria-controls="comentariosCollapse" id="toggleComentariosBtn">
                                                    <i class="fa fa-chevron-down me-1" id="iconToggleComentarios"></i>
                                                    <span id="textToggleComentarios">Mostrar</span>
                                            </button>
                                        </div>

                                        <div class="collapse" id="comentariosCollapse">
                                            <div class="border rounded p-3" style="max-height: 280px; overflow-y: auto;">
                                                @foreach ($retiro->comentarios as $comentario)
                                                    <div class="comentario mb-2">
                                                        <small class="text-muted">[{{ $comentario->created_at_format }}] {{ $comentario->usuario->nombre_usuario }} {{ $comentario->usuario->apellidos_usuario }}:</small>
                                                        <p class="mb-0">{{ $comentario->comentario }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Historial de Cambios del Retiro -->
                            @if ($retiro->historial->isNotEmpty())
                                <div class="my-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">Historial de Cambios del Retiro</h5>
                                        <button class="btn btn-sm btn-outline-secondary d-flex align-items-center" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#cambiosCollapse"
                                                aria-expanded="true" aria-controls="cambiosCollapse" id="toggleCambiosBtn">

                                            <i class="fa fa-chevron-down me-1" id="iconToggleCambios"></i>
                                            <span id="textToggleCambios">Mostrar</span>
                                        </button>
                                    </div>

                                    <div class="collapse" id="cambiosCollapse">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th>Fecha y Hora del retiro</th>
                                                        <th>Tipo de retiro</th>
                                                        <th>Kilogramos estimados</th>
                                                        <th>¿Requiere reposición de Bins?</th>
                                                        <th>Cantidad de Bins a reponer</th>
                                                        <th>Fecha y Hora del cambio</th>
                                                        <th>Usuario que realizó el cambio</th>
                                                        <!-- <th>Motivo del cambio</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($retiro->historial as $registro)
                                                        <tr>
                                                            <td>{{ $registro->fecha_retiro_format }}</td>
                                                            <td>{{ $registro->tipoRetiro->nombre ?? '—' }}</td>
                                                            <td>{{ $registro->kilogramos_estimados }}</td>
                                                            <td>{{ $registro->requiere_reposicion ? 'Sí' : 'No' }}</td>
                                                            <td>{{ $registro->cantidad_bins ?? '—' }}</td>
                                                            <td>{{ $registro->created_at_format }}</td>
                                                            <td>{{ $registro->usuario->nombre_usuario }} {{ $registro->usuario->apellidos_usuario }}</td>
                                                            <!-- <td>{{ $registro->motivo_cambio ?? '—' }}</td> -->
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer">
                            <button type="button" class="btn btn-primary my-2" id="btnActualizar" disabled>
                                <i class="fa fa-save"></i> Guardar Cambios
                            </button>

                            <a href="{{ route('solicitudes-retiro.index') }}" class="btn btn-secondary my-2">
                                <i class="fa fa-times"></i> Cancelar
                            </a>
                        </div>

                        <input type="hidden" name="comentario" id="comentario">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar actualización del retiro -->
    <div class="modal fade" id="confirmActualizacionModal" tabindex="-1" aria-labelledby="confirmActualizacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="confirmActualizacionLabel">Confirmación de Actualización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body text-center">
                    <i class="fas fa-pen-alt text-warning mb-3" style="font-size: 4rem;"></i>

                    <p class="fs-5">Está a punto de actualizar los datos de este retiro.</p>
                    <p class="text-muted small mb-3">Para continuar, debe ingresar el motivo de esta actualización.</p>

                    <div class="form-group px-3">
                        <textarea name="comentario_modal" id="comentario_modal" class="form-control w-100" rows="4" placeholder="Ingrese aquí el motivo de la actualización..." style="resize: vertical;"></textarea>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>

                    <button type="button" class="btn btn-warning text-dark" id="btnConfirmarActualizacion" disabled>
                        <i class="fa fa-check-circle"></i> Confirmar Actualización
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

    {{-- Bootstrap al select2 --}}
    <script>
        window.onload = function() {
            $(".select2").select2({theme: "bootstrap"});
        }
    </script>

    <!-- Sincronización de constantes JS con parámetros de contexto de ejecución de vista  -->
    <script>
        window.contextoVista = {
            esCreate:        {{ $esCreate ? 'true' : 'false' }},
            esEdit:          {{ $esEdit ? 'true' : 'false' }}
        };

        // console.log('📦 En el bloque JS de rescate de sicronización de constantes JS con parámetros de contexto de ejecución de vista');
        // console.log('esCreate:',        window.contextoVista.esCreate);
        // console.log('esEdit:',          window.contextoVista.esEdit);
    </script>

    <!-- Inyectamos objeto JS global para manejo del contexto de región y despliegue correcto de parámetros del retiro -->
	<script>
		window.regionOperativa = {{ $retiro->solicitud->region_operativa_id }};
    </script>

	<!-- JS para coordinar campos tipo_retiro, requiere_reposicion y cantidad_bins en el formulario de Solicitud de Retiro -->
    <script src="{{ asset('js/solicitud-retiro-form-controls.js') }}"></script>

	<!-- Función que activa la estructura válida del Partial Blade : Región X o Región XII -->
	<script src="{{ asset('js/retiro-region-operativa-wrapper.js') }}"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			if (window.regionOperativa) {
				aplicarRegionOperativa(window.regionOperativa);
			}
		});
	</script>

    <!-- Sincronización Hidden antes del Submit y Carga de arreglos OLDs por inicio o por rebote -->
    <script>
        // Rescate de valores OLD al llegar por rebote de error en el Back. Si no es rebote todos quedan en null array vacío.
        window.oldValues = {
            kilogramos_estimados_hidden: @json(old('kilogramos_estimados_hidden')),     // Este es necesario para pintar blade
            tipo_retiro: @json(old('tipo_retiro')),                                     // Este es necesario para el toggleTipoRetiro
            requiere_reposicion_hidden: @json(old('requiere_reposicion_hidden')),       // Este es necesario para pintar blade
            cantidad_bins_hidden: @json(old('cantidad_bins_hidden')),                   // Este es necesario para pintar blade
            tipo_operacion_hidden: @json(old('tipo_operacion_hidden')),                  // Este es necesario para pintar blade
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

    <!-- Pintado de campos modificados en el Historial de Retiros -->
    <script>
        function resaltarCambios(retiroActual, historial) {
            for (let i = 0; i < historial.length; i++) {
                const anterior = historial[i];
                const comparado = (i === 0) ? retiroActual : historial[i - 1];

                const fila = document.querySelectorAll('#cambiosCollapse tbody tr')[i];
                if (!fila) continue;

                const campos = [
                    ['fecha_retiro', 0],
                    ['tipo_retiro_id', 1],
                    ['kilogramos_estimados', 2],
                    ['requiere_reposicion', 3],
                    ['cantidad_bins', 4]
                ];

				campos.forEach(([campo, index]) => {
					const valorAnt = anterior[campo];
					const valorNuevo = comparado[campo];

					let distinto = false;
					switch( campo ){
						case 'requiere_reposicion':
							distinto = Boolean(valorAnt) != Boolean(valorNuevo);
							break;

						case 'cantidad_bins':
							distinto = parseInt(valorAnt ?? 0) != parseInt(valorNuevo ?? 0);
							break;

						default:
							distinto = String(valorAnt ?? '') != String(valorNuevo ?? '');
							break;
					}

					if (distinto) {
						fila.children[index].classList.add('bg-cambio');
					}
				});
            }
        }

        $(document).ready(function () {
            const tieneHistorial = @json($retiro->historial->isNotEmpty());

            if (tieneHistorial) {
                const retiroActual = {
                    fecha_retiro: @json($retiro->fecha_retiro),
                    tipo_retiro_id: @json($retiro->tipo_retiro_id),
                    kilogramos_estimados: @json($retiro->kilogramos_estimados),
                    requiere_reposicion: @json($retiro->requiere_reposicion),
                    cantidad_bins: @json($retiro->cantidad_bins),
                };

                const historial = @json($retiro->historial);

                resaltarCambios(retiroActual, historial);
            }
        });
    </script>

    <!-- Detección de cambios para activar o no el botón de Actualizar : 'Guardar Cambios' -->
    <script>
        function evaluarCambiosFormulario(element) {
            let original;
            let actual;

            switch (element.type) {
                case 'checkbox':
                    original = (element.dataset.original === '1');
                    actual = element.checked; // Evaluamos si está Chequeado nomás.
                    // console.log(`[CheckBox] original=${original} | actual=${actual} | id=${element.id}`);
                    break;

                case 'number':
                    original = parseFloat(element.dataset.original);
                    actual   = parseFloat(element.value);
                    // console.log(`[Number] original=${original} | actual=${actual} | id=${element.id}`);
                    break;

                case 'datetime-local':
                    original = element.dataset.original?.substring(0, 16).replace(' ', 'T');
                    actual   = element.value;
                    // console.log(`[DateTime] original=${original} | actual=${actual} | id=${element.id}`);
                    break;

                case 'select-one':
                    original = element.dataset.original;
                    actual   = element.value;
                    if (!$(element).data('modificado')) {                           // Si el select2 tipo_retiro NO ha sido modificado aún
                        let retiroItem = $(element).closest('.retiro-item');        // Nos fijamos en el hidden...
                        actual = retiroItem.find('.tipo_retiro_original').val();    // ...que posee el valor original de la base de datos
                    }
                    // console.log(`[Select-2] original=${original} | actual=${actual} | id=${element.id}`);
                    break;

                default:
                    original = element.dataset.original;
                    actual   = element.value;
                    // console.log(`[OTRO] original=${original} | actual=${actual} | id=${element.id}`);
                    break;
            }

            let disableBoton = true;
            if (actual !== original) {
                // console.log(`Distintos ...`);
                element.dataset.modificado = 'true';   // Este elemento cambio
                disableBoton = false;               // Este cambio basta para que el boton quede activo (disable = false). Fin !!!

            } else {
                // console.log(`Iguales ...`);
                element.dataset.modificado = 'false';  // No hay cambio, revisar el resto buscando alguno que SI haya cambiado. Por eso usamos .some() en lugar de .forEach() (shortcut en la evaluación positiva).
                disableBoton = ![...document.querySelectorAll('input[data-original], select[data-original], textarea[data-original]')].some(el => el.dataset.modificado === 'true');
            }

            const btnActualizar = document.getElementById('btnActualizar');
            if (btnActualizar) {
                btnActualizar.disabled = disableBoton;
            }
        }

        // Evaluación dinámica ante cambios en los campos
        $(document).on('change', 'input[data-original], select[data-original], textarea[data-original]', function () {
            evaluarCambiosFormulario(this);
        });

        // Evaluación inicial al cargar el DOM
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('input[data-original], select[data-original], textarea[data-original]')
                    .forEach(el => evaluarCambiosFormulario(el));
        });
    </script>

	<!-- Solicitud de Comentario para efectuar la Actualización -->
    <script>
        // Abrir modal cuando se presione Guardar Cambios
        document.getElementById('btnActualizar').addEventListener('click', function () {

            // Antes de continuar se fuerza la validación nativa HTML5 explícitamente
            const form = document.getElementById('formSolicitud');

            // Por si hay errores de validación → se muestra tooltips y NO abre modal
            if (!form.checkValidity()) {
                form.reportValidity();      // muestra los tooltips nativos
                return;
            }

            const comentario = document.getElementById('comentario_modal');
            comentario.value = '';
            document.getElementById('btnConfirmarActualizacion').disabled = true;
            const modal = new bootstrap.Modal(document.getElementById('confirmActualizacionModal'));
            modal.show();
        });

        // Habilitar botón si hay texto
        document.getElementById('comentario_modal').addEventListener('input', function () {
            document.getElementById('btnConfirmarActualizacion').disabled = !this.value.trim();
        });

        // Confirmar y enviar el form con comentario
        document.getElementById('btnConfirmarActualizacion').addEventListener('click', function () {
            const comentario = document.getElementById('comentario_modal').value.trim();
            document.getElementById('comentario').value = comentario;
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enviando...';

            $('#formSolicitud').trigger('submit');
        });
    </script>
@endsection
