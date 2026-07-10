@extends('layouts.app')

@section('head-scripts')
    <!-- DataTables + Bootstrap 5 (CDN o local) y DataTables Buttons (botones para exportar y más) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS para Sincronizar scrolls en DataTable y estilos para botones-toggle en filtros -->
    <link rel="stylesheet" href="{{ asset('css/scrolls-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-dataTables.css') }}">

	<!-- CSS para PAGINADORES de dataTables en ventanas angostas -->
    <style>
		/* Si la pantalla es muy pequeña, reducir padding horizontal y tamaño de fuente en botones de paginación */
		@media (max-width: 480px) {
			#dt-paginate .pagination {
				--bs-pagination-padding-x: 0.50rem;
		        --bs-pagination-font-size: 0.90rem;
			}
		}
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white fs-5">
                    Listado de Camiones
                </div>
                <div class="card-body">
                    @include('includes.alertas-sistema')

                    <div class="form-group col-md-12 mb-2">
                        <button type="button" class="btn btn-primary mb-2" onclick="window.location.href='{{ route('camion.create') }}'">
                            <i class="fa fa-plus"></i> Nuevo Camión
                        </button>
                    </div>

                    <div id="camionesTable" class="datatable-contenedor-externo">

                        <!-- Controles ARRIBA -->
                        <div class="row datatable-controles-superiores mb-2 gy-2">
                            <!-- Filtros (izquierda) -->
                            <div class="col-12 col-md-8">
                                <div id="Estado" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                    <label class="fw-bold mb-0">Filtrar por Estado:</label>

                                    <div class="filtros-grid mt-2" role="group" aria-label="Filtro Estado">
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado1" value="Habilitado" autocomplete="off" checked>
                                            <label class="btn filtro-btn-primary w-100" for="estado1">Habilitado</label>
                                        </div>
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado2" value="Deshabilitado" autocomplete="off">
                                            <label class="btn filtro-btn-primary w-100" for="estado2">Deshabilitado</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="Arrendado" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                    <label class="fw-bold mb-0">Filtrar por Arrendado:</label>

                                    <div class="filtros-grid mt-2" role="group" aria-label="Filtro Arrendado">
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="arrendado1" value="Sí" autocomplete="off" checked>
                                            <label class="btn filtro-btn-secondary w-100" for="arrendado1">Sí</label>
                                        </div>
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="arrendado2" value="No" autocomplete="off" checked>
                                            <label class="btn filtro-btn-secondary w-100" for="arrendado2">No</label>
                                        </div>
                                    </div>
                                </div>

								<div id="Región" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
									<label class="fw-bold mb-0">Filtrar por Región Operativa:</label>

									<div class="filtros-grid mt-2" role="group" aria-label="Filtro Región">
										<div class="col-4 col-sm-auto">
											<input type="checkbox" class="btn-check filtro-toggle" id="region1" value="X" autocomplete="off" checked>
											<label class="btn filtro-btn-info w-100" for="region1">X Region</label>
										</div>
										<div class="col-4 col-sm-auto">
											<input type="checkbox" class="btn-check filtro-toggle" id="region2" value="XII" autocomplete="off" checked>
											<label class="btn filtro-btn-info w-100" for="region2">XII Region</label>
										</div>
									</div>
								</div>
							</div>

                            <!-- Controles DataTable (derecha) -->
                            <div class="col-12 col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div id="dt-length" class="w-100"></div>
                                    </div>
                                    <div class="col-6">
                                        <div id="dt-filter" class="w-100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SCROLL SUPERIOR -->
                        <div class="top-scroll"></div>

                        <!-- Tabla con scroll horizontal -->
                        <div class="table-scroll">
                            <table class="table table-striped table-bordered listado d-none">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 1%;" class="text-nowrap">Estado</th>
                                        <th>Patente</th>
										<th>Región</th>
                                        <th>Tipo</th>
                                        <th>Arrendado</th>
                                        <th>Empresa</th>
                                        <th>Conductor</th>
                                        <th>Rampla</th>
                                        <th width="5%" class="nosort">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($camiones as $camion)
                                        <tr>
                                            <td style="width: 1%;" class="text-nowrap text-center">
                                                @if ($camion->activo)
                                                    <i class="fas fa-check-circle text-success fs-4"></i><div>Habilitado</div>
                                                @else
                                                    <i class="fas fa-times-circle text-danger fs-4"></i><div>Deshabilitado</div>
                                                @endif
                                            </td>
                                            <td>{{ $camion->patente }}</td>
											<td class="text-center">{{ $camion->region_operativa_codigo }}</td>
                                            <td>{{ $camion->tipoCamion->nombre ?? '-' }}</td>
                                            <td class="text-center">{{ $camion->arrendado ? 'Sí' : 'No' }}</td>
                                            <td>{{ $camion->empresa->razon_social ?? '-' }}</td>
                                            <td>{{ $camion->conductor->nombre ?? '-' }} {{ $camion->conductor->apellido ?? '' }}</td>
                                            <td>{{ $camion->patente_rampla ?? 'Sin rampla' }}</td>
                                            <td>
                                                <div class="d-grid gap-1">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-info btn-xs text-white btnShowCamion" data-id="{{ $camion->id }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>

                                                        @if ($camion->activo)
                                                            <a class="btn btn-warning btn-xs text-white" href="{{ route('camion.edit', ['camion' => Crypt::encrypt($camion->id)]) }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn btn-warning btn-xs text-white btnEditarInactivo"">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @if ($camion->activo)
                                                            <button type="button" class="btn btn-danger btn-xs btnDeleteCamion" data-id="{{ $camion->id }}" data-activo="{{ $camion->activo ? '1' : '0' }}">
                                                                <i class="fas fa-minus-circle"></i> {{-- desactivar --}}
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-danger btn-xs btnDeleteCamion" data-id="{{ $camion->id }}" data-activo="{{ $camion->activo ? '1' : '0' }}">
                                                                <i class="fas fa-check-circle"></i> {{-- reactivar --}}
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- SCROLL INFERIOR -->
                        <div class="bottom-scroll"></div>

                        <!-- Controles ABAJO -->
                        <div class="row mb-2 gy-2 mt-2">
                            <!-- Bloque de Cantidad de Registros Mostrados (izquierda ventana amplia - centro ventana estrecha) -->
                            <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-start">
                                <div class="row g-2">
                                    <div id="dt-info" class="text-center"></div>
                                </div>
                            </div>

                            <!-- Bloque de Paginador del DataTable (derecha ventana amplia - centro ventana estrecha) -->
                            <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                                <div class="row g-2">
                                    <div id="dt-paginate"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Excel e Impresion/PDF -->
                        <div class="row mb-2 gy-2">
                            <div id="dt-buttons" class="text-center mt-2"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Detalle -->
<div class="modal fade" id="modalCamion" tabindex="-1" aria-labelledby="modalCamionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCamionLabel">Detalle del Camión</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="detailShowCamion"></div>
            <div class="modal-footer">
                <a type="button" id="btnPrintCamion" class="btn btn-primary" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para indicar que registro no-activo no se puede editar -->
<div class="modal fade" id="registroInactivoModal" tabindex="-1" aria-labelledby="registroInactivoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="registroInactivoLabel">Camión Inactivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center">
                <i class="fas fa-ban text-warning mb-3" style="font-size: 4rem;"></i>
                <p class="fs-5">Camión inactivo no puede ser editado.</p>
                <p class="text-muted small mb-0">Para poder realizar modificaciones, primero debe activarlo.</p>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fa fa-check"></i> Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Confirmar Eliminación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 4rem;"></i>
                    <p class="fs-5"></p>


                    <div class="form-group w-100">
                        <p class="text-muted small mb-3">Debe ingresar una observacion para la desactivación.</p>

                        <textarea name="observacion_inactividad"
                                id="observacion_inactividad"
                                class="form-control"
                                rows="3"
                                placeholder="Ingrese observación"
                                style="resize: vertical;"></textarea>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger"></button> <!-- Botón programado abajo en el JS que lanza la Modal -->
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    <!-- JS para DataTables + Bootstrap 5 -->
    <!-- JSZip para exportar a Excel - pdfMake para exportar a PDF -->
    <!-- Botones de DataTables -->
    <!-- Inicialización de dataTable - Scroll Horizontal y Filtros -->
    @include('includes.datatable-scripts')
    <script>
        $(document).ready(function () {
            inicializarDataTable('camionesTable');
        });

        $(window).on('resize', function () {
            actualizarScrollHorizontal('camionesTable');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.min.js"></script>
    @include('actores.camion.template-handlebars-show')

    <script>
        const modalCamion = new bootstrap.Modal(document.getElementById('modalCamion'));

        $(document).on("click", ".btnShowCamion", function () {
            const id = $(this).data("id");
            $.get(`/actores/camion/${id}`, function (data) {
                const source = document.getElementById("template-show-camion").innerHTML;
                const template = Handlebars.compile(source);
                const html = template(data);
                $("#detailShowCamion").html(html);
                modalCamion.show();
            });
        });

        document.getElementById("btnPrintCamion").addEventListener("click", function (e) {
            e.preventDefault();
            const emblemaURL = "{{ url('/config/'.$designParameter['emblema_design']) }}";
            const contenido = document.getElementById("detailShowCamion").innerHTML;
            const ventana = window.open("", "_blank", "width=800,height=600");

            ventana.document.write(`
                <html>
                    <head>
                        <title>Imprimir Detalle del Camión</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            @page {
                                size: letter portrait;
                                margin: 20mm;
                            }
                            body { padding: 2rem; font-size: 14px; }
                            img { max-width: 100px; height: auto; }
                            .emblema-print {
                                position: absolute;
                                top: 20px;
                                left: 20px;
                                max-height: 100px;
                            }
                        </style>
                    </head>
                    <body>
                        <img src="${emblemaURL}" alt="Emblema" class="emblema-print">
                        <h3 class="mb-4 text-center">Detalle del Camión</h3>
                        <div style="margin-top: 120px;">
                            ${contenido}
                        </div>
                        <script>
                            window.onload = function() {
                                window.print();
                                window.onafterprint = function () {
                                    window.close();
                                };
                            }
                        <\/script>
                    </body>
                </html>
            `);

			ventana.document.close();
        });
    </script>

    <script>
        const registroInactivoModal = new bootstrap.Modal(document.getElementById("registroInactivoModal"));
        $(document).on("click", ".btnEditarInactivo", function () {
            registroInactivoModal.show();
        });
    </script>

    <!-- Procesamiento de la ventana modal de Anulación -->
    <script>
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        $(document).on("click", ".btnDeleteCamion", function () {
            // Asignación de la acción al Form
            const id = this.dataset.id;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/actores/camion/${id}`;

            // Para asignaciones subordinadas a si está Activo o Desactivo
            const activo = this.dataset.activo === '1';

            // Título y pregunta
            const titulo = document.getElementById('confirmDeleteLabel');
            titulo.innerText = `Confirmar ${activo ? 'Desactivación' : 'Reactivación'}`;
            const pregunta = document.querySelector('#confirmDeleteModal .modal-body p.fs-5');
            pregunta.innerText = `¿Está seguro que desea ${activo ? 'desactivar' : 'reactivar'} este camión?`;

            // Mostrar u ocultar campo de observación
            const textarea = document.getElementById('observacion_inactividad');
            const textareaGroup = textarea.closest('.form-group');
            if (activo) {
                textareaGroup.classList.remove('d-none');
            } else {
                textareaGroup.classList.add('d-none');
            }
            // Actualizar texto e ícono del botón
            const botonSubmit = deleteForm.querySelector('button[type="submit"]');
            botonSubmit.innerHTML = `<i class="fas ${activo ? 'fa-ban' : 'fa-check-circle'}"></i> ${activo ? 'Desactivar' : 'Reactivar'}`;

            confirmDeleteModal.show();
        });
    </script>

    <!-- Captura del Submit para colocar Spinner en el Botón y evitar doble Submit -->
    <script>
		$('#deleteForm').on('submit', function (e) {
			// 🔁 Controlar doble submit
			// Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
			if (this.enviado) {
				console.log("⛔ Doble submit detectado, se detiene.");
				e.preventDefault();
				return;
			}

			const $btnSubmit = $('#deleteForm button[type="submit"]');
			if ($btnSubmit.length) {
				// Deshabilita el botón para evitar segundo click
			    $btnSubmit.prop('disabled', true);
			    $btnSubmit.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
			}

			// 🧷 Flag para evitar siguiente submit
			this.enviado = true;
		});
    </script>
@endsection
