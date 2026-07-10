@extends('layouts.app')

@section('head-scripts')
    <!-- DataTables + Bootstrap 5 (CDN o local) y DataTables Buttons (botones para exportar y más) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS para Sincronizar scrolls en DataTable y estilos para botones-toggle en filtros -->
    <link rel="stylesheet" href="{{ asset('css/scrolls-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-dataTables.css') }}">

    <!-- CSS para Select2 dentro de Modal de Vinculación con Plantas -->
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/personalizaciones-select2.css') }}">

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
                    Listado de Empresas
                </div>
                <div class="card-body">
                    @include('includes.alertas-sistema')

                    <div class="form-group col-md-12 mb-2">
                        <button type="button" class="btn btn-primary mb-2" onclick="window.location.href='{{ route('empresa.create') }}'">
                            <i class="fa fa-plus"></i> Nueva Empresa
                        </button>
                    </div>

                    <div id="empresasTable" class="datatable-contenedor-externo">

                        <!-- Controles ARRIBA -->
                        <div class="row datatable-controles-superiores mb-2 gy-2">
                            <!-- Bloque de Filtros (izquierda) -->
                            <div class="col-12 col-md-8">
                                <div id="Estado" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                    <label class="fw-bold mb-0">Filtrar por Estado:</label>

                                    <div class="filtros-grid mt-2" role="group" aria-label="Filtro Estado">
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado1" value="Habilitada" autocomplete="off" checked>
                                            <label class="btn filtro-btn-primary w-100" for="estado1">Habilitada</label>
                                        </div>
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado2" value="Deshabilitada" autocomplete="off">
                                            <label class="btn filtro-btn-primary w-100" for="estado2">Deshabilitada</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="Tipo" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                    <label class="fw-bold mb-0">Filtrar por Tipo:</label>

                                    <div class="filtros-grid mt-2" role="group" aria-label="Filtro Tipo">
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="tipo1" value="Productor" autocomplete="off" checked>
                                            <label class="btn filtro-btn-secondary w-100" for="tipo1">Productor</label>
                                        </div>
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="tipo2" value="Transportista" autocomplete="off" checked>
                                            <label class="btn filtro-btn-secondary w-100" for="tipo2">Transportista</label>
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
                                        <th>Tipo</th>
                                        <th>RUT</th>
                                        <th>Razón Social</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                        <th>Comuna</th>
                                        <th>Región</th>
                                        <th width="5%" class="nosort">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($empresas as $empresa)
                                        <tr>
                                            <td style="width: 1%;" class="text-nowrap text-center">
                                                @if ($empresa->activo)
                                                    <i class="fas fa-check-circle text-success fs-4"></i><div>Habilitada</div>
                                                @else
                                                    <i class="fas fa-times-circle text-danger fs-4"></i><div>Deshabilitada</div>
                                                @endif
                                            </td>
                                            <td>{{ $empresa->tipoEmpresa->nombre ?? '-' }}</td>
                                            <td>{{ $empresa->rut_empresa }}</td>
                                            <td>{{ $empresa->razon_social }}</td>
                                            <td>{{ $empresa->telefono }}</td>
                                            <td>{{ $empresa->direccion }}</td>
                                            <td>{{ $empresa->comuna->nombre }}</td>
                                            <td>{{ $empresa->comuna->region->nombre }}</td>
                                            <td>
                                                <div class="d-grid gap-1">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-info btn-xs text-white btnShowEmpresa" data-id="{{ $empresa->id }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>

                                                        @if ($empresa->activo)
                                                            <a class="btn btn-warning btn-xs text-white" href="{{ route('empresa.edit', ['empresa' => Crypt::encrypt($empresa->id)]) }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn btn-warning btn-xs text-white btnEditarInactivo"">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @if( ($empresa->tipo_empresa_id == config('constantes.TIPO_EMPRESA_PRODUCTORA')) && ($empresa->activo) )
                                                            <button type="button" class="btn btn-secondary btn-xs text-white btnVinculaPlantas" data-id="{{ $empresa->id }}">
                                                                <i class="fa fa-industry"></i>
                                                            </button>
                                                        @endif

                                                        @if ($empresa->activo)
                                                            <button type="button" class="btn btn-danger btn-xs btnDeleteEmpresa" data-id="{{ $empresa->id }}" data-activo="{{ $empresa->activo ? '1' : '0' }}">
                                                                <i class="fas fa-minus-circle"></i> {{-- desactivar --}}
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-danger btn-xs btnDeleteEmpresa" data-id="{{ $empresa->id }}" data-activo="{{ $empresa->activo ? '1' : '0' }}">
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

<!-- Modal: Detalle de Empresa -->
<div class="modal fade" id="modalEmpresa" tabindex="-1" aria-labelledby="modalEmpresaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEmpresaLabel">Detalle de la Empresa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="detailShowEmpresa"></div>
            <div class="modal-footer">
                <a type="button" id="btnPrintEmpresa" class="btn btn-primary" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Vincula Plantas de Proceso -->
<div class="modal fade" id="modalVinculaPlantas" tabindex="-1" aria-labelledby="modalVinculaPlantasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('empresa.plantas.guardar', ['empresa' => '__ID__']) }}" method="POST" id="formVinculaPlantas">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalVinculaPlantasLabel">Vinculaciones con Plantas de Proceso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Solo un modal-body -->
                <div class="modal-body" id="detailVinculaPlantas"></div>

                <div class="modal-footer d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-floppy-o"></i> Guardar Cambios
                        </button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Modal para indicar que registro no-activo no se puede editar -->
<div class="modal fade" id="registroInactivoModal" tabindex="-1" aria-labelledby="registroInactivoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="registroInactivoLabel">Empresa Inactiva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center">
                <i class="fas fa-ban text-warning mb-3" style="font-size: 4rem;"></i>
                <p class="fs-5">Empresa inactiva no puede ser editada.</p>
                <p class="text-muted small mb-0">Para poder realizar modificaciones, primero debe activarla.</p>
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
    <!-- Select2 JavaScript (extensión de elementos <select> con búsqueda, multiselección, etc.) -->
    <script src="{{ asset('js/select2.full.js') }}"></script>
    @include('includes.constantes-js-catalogo')

    <!-- JS para DataTables + Bootstrap 5 -->
    <!-- JSZip para exportar a Excel - pdfMake para exportar a PDF -->
    <!-- Botones de DataTables -->
    <!-- Inicialización de dataTable - Scroll Horizontal y Filtros -->
    @include('includes.datatable-scripts')
    <script>
        $(document).ready(function () {
            inicializarDataTable('empresasTable');
        });

        $(window).on('resize', function () {
            actualizarScrollHorizontal('empresasTable');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.min.js"></script>
    @include('actores.empresa.template-handlebars-show')
    <script>
        const modalEmpresa = new bootstrap.Modal(document.getElementById('modalEmpresa'));

        $(document).on("click", ".btnShowEmpresa", function () {
            const id = $(this).data("id");
            $.get(`/actores/empresa/${id}`, function (data) {
                const source = document.getElementById("template-show-empresa").innerHTML;
                const template = Handlebars.compile(source);
                const html = template(data);
                console.log(data);
                $("#detailShowEmpresa").html(html);
                modalEmpresa.show();
            });
        });

        document.getElementById("btnPrintEmpresa").addEventListener("click", function (e) {
            e.preventDefault();
            const emblemaURL = "{{ url('/config/'.$designParameter['emblema_design']) }}";
            const contenido = document.getElementById("detailShowEmpresa").innerHTML;
            const ventana = window.open("", "_blank", "width=800,height=600");

            ventana.document.write(`
                <html>
                    <head>
                        <title>Imprimir Detalle de la Empresa</title>
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
                        <h3 class="mb-4 text-center">Detalle de la Empresa</h3>
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

    <!-- Mopdal de vinculación con plantas - Maquilas -->
    @include('actores.empresa.template-handlebars-plantas')
	<script>
        const modalVinculaPlantas = new bootstrap.Modal(document.getElementById('modalVinculaPlantas'));
        $(document).on("click", ".btnVinculaPlantas", function () {
            const id = $(this).data('id');

            $.get(`/empresa/${id}/plantas`, function(data) {
                const source = document.getElementById("template-vincula-plantas").innerHTML;
                const template = Handlebars.compile(source);
                const html = template(data);

                $("#detailVinculaPlantas").html(html);
                $('#formVinculaPlantas').attr('action', `/empresa/${data.empresa.id}/plantas`);

                // 1. Rellenar el select2 con plantas NO asociadas
                const select = $('#plantaProceso_id');
                select.empty().append('<option value="">Seleccione planta ...</option>');

                data.sucursales.forEach(sucursal => {
                    if (!data.asociadas.includes(sucursal.id)) {
                        select.append(`<option value="${sucursal.id}">${sucursal.nombre_sucursal}</option>`);
                    }
                });

                select.select2({
                    theme: "bootstrap",                         // Reactivar select2 después de render
                    dropdownParent: $('#modalVinculaPlantas')   // 👈 Clave para evitar problemas de z-index y no se despliegue por debajo de la modal
                });

                // 2. Llenar lista de plantas actualmente asociadas
                const lista = $('#plantaProceso-asociadas');
                const hidden = $('#sucursales_asociadas');

                let plantasSeleccionadas = [];

                data.sucursales.forEach(sucursal => {
                    if (data.asociadas.includes(sucursal.id)) {
                        // Agregar a la lista visual
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.dataset.id = sucursal.id;
                        li.innerHTML = `
                            ${sucursal.nombre_sucursal}
                            <button type="button" class="btn btn-danger btn-sm btnQuitar">
                                <i class="fa fa-times"></i>
                            </button>`;
                        lista.append(li);

                        plantasSeleccionadas.push(sucursal.id);
                    }
                });

                // 3. Reflejar en el input hidden
                function actualizarHidden() {
                    const contenedor = $('#contenedor-hidden');
                    contenedor.empty();
                    plantasSeleccionadas.forEach(id => {
                        contenedor.append(`<input type="hidden" name="sucursales[]" value="${id}">`);
                    });
                }
                actualizarHidden();

                // 4. Evento para agregar nueva planta desde select
                $('#btn-agregar-plantaProceso').on('click', function () {
                    const selectedID = parseInt(select.val());
                    const selectedText = select.find('option:selected').text();

                    if (!selectedID || plantasSeleccionadas.includes(selectedID)) return;

                    // Agregar a la lista visual
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.dataset.id = selectedID;
                    li.innerHTML = `
                        ${selectedText}
                        <button type="button" class="btn btn-danger btn-sm btnQuitar">
                            <i class="fa fa-times"></i>
                        </button>`;
                    lista.append(li);

                    plantasSeleccionadas.push(selectedID);
                    actualizarHidden();

                    // Remover la opción del select
                    select.find(`option[value="${selectedID}"]`).remove();
                    select.val('').trigger('change');
                });

                // 5. Evento para quitar de la lista
                lista.on('click', '.btnQuitar', function () {
                    const li = $(this).closest('li');
                    const id = parseInt(li.data('id'));
                    const nombre = li.text().trim();

                    // Eliminar del array
                    plantasSeleccionadas = plantasSeleccionadas.filter(pid => pid !== id);
                    actualizarHidden();

                    // Remover de la lista visual
                    li.remove();

                    // Volver a agregar al select
                    select.append(`<option value="${id}">${nombre}</option>`);

                    select.select2({
                        theme: "bootstrap",                         // Reactivar select2 después de agregarlo
                        dropdownParent: $('#modalVinculaPlantas')   // 👈 Clave para evitar problemas de z-index y no se despliegue por debajo de la modal
                    });
                });

                modalVinculaPlantas.show();
            });
        });
    </script>
    <!-- Captura del Submit para colocar Spinner en el Botón y evitar doble Submit -->
    <script>
		$('#formVinculaPlantas').on('submit', function (e) {
			// 🔁 Controlar doble submit
			// Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
			if (this.enviado) {
				console.log("⛔ Doble submit detectado, se detiene.");
				e.preventDefault();
				return;
			}

			const $btnSubmit = $('#formVinculaPlantas button[type="submit"]');
			if ($btnSubmit.length) {
				// Deshabilita el botón para evitar segundo click
			    $btnSubmit.prop('disabled', true);
			    $btnSubmit.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
			}

			// 🧷 Flag para evitar siguiente submit
			this.enviado = true;
		});
    </script>

    <script>
        const registroInactivoModal = new bootstrap.Modal(document.getElementById("registroInactivoModal"));
        $(document).on("click", ".btnEditarInactivo", function () {
            registroInactivoModal.show();
        });
    </script>

    <!-- Manejo del Borrado desde Modal de Eliminación -->
    <script>
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        $(document).on("click", ".btnDeleteEmpresa", function () {
            // Asignación de la acción al Form
            const id = this.dataset.id;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/actores/empresa/${id}`;

            // Para asignaciones subordinadas a si está Activo o Desactivo
            const activo = this.dataset.activo === '1';

            // Título y pregunta
            const titulo = document.getElementById('confirmDeleteLabel');
            titulo.innerText = `Confirmar ${activo ? 'Desactivación' : 'Reactivación'}`;
            const pregunta = document.querySelector('#confirmDeleteModal .modal-body p.fs-5');
            pregunta.innerText = `¿Está seguro que desea ${activo ? 'desactivar' : 'reactivar'} esta empresa?`;

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
