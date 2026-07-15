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
                    Listado de Usuarios
                </div>
                <div class="card-body">
                    @include('includes.alertas-sistema')

                    <div class="form-group col-md-12 mb-2">
                        <button type="button" class="btn btn-primary mb-2" onclick="window.location.href='{{ url('actores/usuario/create') }}'">
                            <i class="fa fa-plus"></i> Nuevo Usuario
                        </button>
                    </div>

                    <div id="usuariosTable" class="datatable-contenedor-externo">

                        <!-- Controles ARRIBA -->
                        <div class="row datatable-controles-superiores mb-2 gy-2">
                            <!-- Filtros (izquierda) -->
                            <div class="col-12 col-md-8">
                                <div id="Estado" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                    <label class="fw-bold mb-0">Filtrar por Estado:</label>

                                    <div class="filtros-grid mt-2" role="group" aria-label="Filtro Estado">
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado1" value="Vigente" autocomplete="off" checked>
                                            <label class="btn filtro-btn-primary w-100" for="estado1">Vigente</label>
                                        </div>
                                        <div class="col-4 col-sm-auto">
                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado2" value="No vigente" autocomplete="off">
                                            <label class="btn filtro-btn-primary w-100" for="estado2">No vigente</label>
                                        </div>
                                    </div>
                                </div>

								<div id="R.Operativa" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
									<label class="fw-bold mb-0">Filtrar por Región Operativa:</label>

									<div class="filtros-grid mt-2" role="group" aria-label="Filtro Región">
										<div class="col-4 col-sm-auto">
											<input type="checkbox" class="btn-check filtro-toggle" id="region1" value="X" autocomplete="off" checked>
											<label class="btn filtro-btn-secondary w-100" for="region1">X Region</label>
										</div>
										<div class="col-4 col-sm-auto">
											<input type="checkbox" class="btn-check filtro-toggle" id="region2" value="XII" autocomplete="off" checked>
											<label class="btn filtro-btn-secondary w-100" for="region2">XII Region</label>
										</div>
										<div class="col-4 col-sm-auto">
											<input type="checkbox" class="btn-check filtro-toggle" id="region3" value="X/XII" autocomplete="off" checked>
											<label class="btn filtro-btn-secondary w-100" for="region3">X/XII Region</label>
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
                                        <th width="5%">Avatar</th>
                                        <th>Rol</th>
                                        <th>R.Operativa</th>
                                        <th>Planta/Productor</th>
                                        <th>Rut</th>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>Correo Electrónico</th>
                                        <th>Teléfono</th>
                                        <th width="5%" class="nosort">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuario as $user)
                                        <tr>
                                            <td style="width: 1%;" class="text-nowrap text-center">
                                                @if ($user->activo)
                                                    <i class="fas fa-check-circle text-success fs-4"></i><div>Vigente</div>
                                                @else
                                                    <i class="fas fa-times-circle text-danger fs-4"></i><div>No vigente</div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <img src="{{ asset('uploads/avatar/' . ($user->avatar ? $user->avatar . '_small.jpg' : 'default_small.jpg')) }}"
                                                    alt="Avatar Pequeño"
                                                    class="rounded-circle"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            </td>
                                            <td>{{ $user->rol->nombre ?? '-' }}</td>
                                            <td>{{ $user->region_operativa_codigo ?? '-' }}</td>
											<td>
                                                @switch($user->rol_id)
                                                    @case(config('constantes.ROL_SOLICITANTE_PLANTA'))
                                                        {{ $user->sucursal->nombre_sucursal ?? '-' }}
                                                        @break

                                                    @case(config('constantes.ROL_SOLICITANTE_PRODUCTOR'))
                                                        {{ $user->empresa->razon_social ?? '-' }}
                                                        @break

                                                    @default
                                                        {{ 'NA' }} {{-- No Aplica porque el rol del usuario no coincide con solicitante en ningún caso --}}
                                                @endswitch
                                            </td>
                                            <td>{{ $user->rut_usuario }}</td>
                                            <td>{{ $user->nombre_usuario }}</td>
                                            <td>{{ $user->apellidos_usuario }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->telefono }}</td>

                                            <td>
                                                <div class="d-grid gap-1">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-info btn-xs text-white btnShowTrabajador" data-id="{{$user->id}}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>

                                                        @if ($user->activo && $manageableUserIds->contains($user->id))
                                                            <a class="btn btn-warning btn-xs text-white" href="{{ route('usuario.edit', ['usuario' => Crypt::encrypt($user->id)]) }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @elseif(!$user->activo && $manageableUserIds->contains($user->id))
                                                            <button type="button" class="btn btn-warning btn-xs text-white btnEditarInactivo"">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @if (! $user->hasVerifiedEmail())
                                                            <button type="button" class="btn btn-secondary btn-xs btnReSendWelcomeEmail" data-id="{{ $user->id }}" title="Reenviar correo de bienvenida">
                                                                <i class="fas fa-envelope"></i> {{-- Reenviar el Correo de Verificación y Bienvenida --}}
                                                            </button>
                                                        @endif

                                                        @if ($user->activo)
                                                            <button type="button" class="btn btn-danger btn-xs btnDeleteTrabajador" data-id="{{ $user->id }}" data-activo="{{ $user->activo ? '1' : '0' }}">
                                                                <i class="fas fa-minus-circle"></i> {{-- desactivar --}}
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-danger btn-xs btnDeleteTrabajador" data-id="{{ $user->id }}" data-activo="{{ $user->activo ? '1' : '0' }}">
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

                        <!-- Scroll horizontal abajo -->
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

<!-- Modal: Examinar Usuario (SHOW) -->
<div class="modal fade" id="modalTrabajador" tabindex="-1" aria-labelledby="modalTrabajadorLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTrabajadorLabel">Detalle del Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Solo un modal-body -->
            <div class="modal-body" id="detailShowTrabajador"></div>

            <div class="modal-footer">
                <a type="button" id="btnPrintTrabajador" class="btn btn-primary" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
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
                <h5 class="modal-title" id="registroInactivoLabel">Usuario Inactivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center">
                <i class="fas fa-ban text-warning mb-3" style="font-size: 4rem;"></i>
                <p class="fs-5">Usuario inactivo no puede ser editado.</p>
                <p class="text-muted small mb-0">Para poder realizar modificaciones, primero debe activarlo.</p>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">
                    <i class="fa fa-check"></i> Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación de usuario -->
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

<!-- Modal de estado para reenvío de correo de verificaicón y bienvenida -->
<div class="modal fade" id="modal-reenvio-correo" tabindex="-1" aria-labelledby="modalReenvioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-dark">
                <h5 class="modal-title" id="modal-reenvio-correo-label">Reenvío de correo de Verificación y Bienvenida</h5>
            </div>

            <div class="modal-body text-center">
                <div id="reenvio-spinner">
                    <i class="fas fa-spinner fa-spin fa-3x text-secondary mb-3" style="font-size: 4rem;"></i>
                    <p class="fs-5">Procesando reenvío de correo…</p>
                </div>

                <div id="reenvio-exito" class="d-none">
                    <i class="fas fa-check-circle text-secondary mb-3" style="font-size: 4rem;"></i>
                    <p class="fs-5">¡Correo reenviado exitosamente!</p>
                </div>

                <div id="reenvio-error" class="d-none">
                    <i class="fas fa-exclamation-triangle text-secondary mb-3" style="font-size: 4rem;"></i>
                    <p class="fs-5">Error al reenviar el correo.</p>
                    <p class="text-muted small mb-0">Quizás el usuario ya activo su cuenta.</p>
                </div>

                <div class="modal-footer justify-content-center p-0" style="min-height: 58px;">
                    <button type="button" class="btn btn-secondary d-none" data-bs-dismiss="modal" id="btn-aceptar">
                        <i class="fa fa-check"></i> Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    <!-- Cargamos las constantes Select2 para manejar roles de solicitante planta/productor en el partial Show de HandleBar) -->
    @include('includes.constantes-js-catalogo')

    <!-- JS para DataTables + Bootstrap 5 -->
    <!-- JSZip para exportar a Excel - pdfMake para exportar a PDF -->
    <!-- Botones de DataTables -->
    <!-- Inicialización de dataTable - Scroll Horizontal y Filtros -->
    @include('includes.datatable-scripts')
	<script>
		$(document).ready(function () {
			inicializarDataTable('usuariosTable');
		});

		$(window).on('resize', function () {
			actualizarScrollHorizontal('usuariosTable');
		});
	</script>

    <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.min.js"></script>

    <!-- Registramos helper 'eq' para comparaciones en el partial (para desplegar condicionalmente roles de solicitantes)-->
    <script>
        Handlebars.registerHelper('eq', function(a, b, options) {
            // Comparación segura
            const result = (a == b); // Usamos == en lugar de === para manejar string vs number

            // Verifica si es una llamada desde un bloque #if
            if (options && options.fn) {
                return result ? options.fn(this) :
                    (options.inverse ? options.inverse(this) : '');
            }

            // Si es una expresión simple, devuelve el booleano
            return result;
        });
    </script>

    @include('actores.usuario.template-handlebars-show')
    <script>
        // Inicialización del modal con configuración especial
        const modalTrabajador = new bootstrap.Modal(document.getElementById('modalTrabajador'));

        // Limpiar el foco antes de ocultar
        document.getElementById('modalTrabajador').addEventListener('hide.bs.modal', function() {
            document.activeElement?.blur();
        });

        $(document).on("click", ".btnShowTrabajador", function () {
            const id = $(this).data("id");
            $.get(`/actores/usuario/${id}`, function (data) {
                // Preparar avatar_url, el que tenga el usuario o el por default y tamaño mediano
                data.avatar_url = data.avatar
                    ? '/uploads/avatar/' + data.avatar + '_medium.jpg'
                    : '/uploads/avatar/default_medium.jpg';

                // Le insertamos los valores de las constantes de roles de usuario
                data.ROL_SOLICITANTE_PLANTA = ROL_SOLICITANTE_PLANTA;
                data.ROL_SOLICITANTE_PRODUCTOR = ROL_SOLICITANTE_PRODUCTOR;

                const source = document.getElementById("template-show-trabajador").innerHTML;
                const template = Handlebars.compile(source);
                const html = template(data);

                $("#detailShowTrabajador").html(html);

                modalTrabajador.show();
            });
        });
    </script>

    <script>
        document.getElementById("btnPrintTrabajador").addEventListener("click", function (e) {
            e.preventDefault();
            const emblemaURL = "{{ url('/config/'.$designParameter['emblema_design']) }}";
            const contenido = document.getElementById("detailShowTrabajador").innerHTML;
            const ventana = window.open("", "_blank", "width=800,height=600");

            ventana.document.write(`
                <html>
                    <head>
                        <title>Imprimir Detalle del Usuario</title>
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
                        <h3 class="mb-4 text-center">Detalle del Usuario</h3>
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

    <script>
        $(document).on('click', '.btnReSendWelcomeEmail', function () {
            let userId = $(this).data('id');
            let $button = $(this);

            // Desactivar el botón del listado
            $button.prop('disabled', true).addClass('disabled');

            let modal = new bootstrap.Modal(document.getElementById('modal-reenvio-correo'),
                                        {
                                            backdrop: 'static',     // No permitir cerrar con backdrop (click fuera del modal)
                                            keyboard: false         // No permitir cerrar con ESC
                                        });

            // Ocultar bloques de resultado y el botón de aceptar
            $('#reenvio-exito').addClass('d-none');
            $('#reenvio-error').addClass('d-none');
            $('#btn-aceptar').addClass('d-none');

            // Mostrar spinner y modal
            $('#reenvio-spinner').removeClass('d-none');
            modal.show();

            $.ajax({
                url: '/usuario/' + userId + '/resend-welcome',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#reenvio-spinner').addClass('d-none');
                    $('#reenvio-exito').removeClass('d-none');
                },
                error: function (xhr) {
                    $('#reenvio-spinner').addClass('d-none');
                    $('#reenvio-error').removeClass('d-none');
                },
                complete: function () {
                    // Reactivar el botón del listado
                    $button.prop('disabled', false).removeClass('disabled');

                    // Mostrar el botón de aceptar
                    $('#btn-aceptar').removeClass('d-none');

                    // Permitir cerrar el modal con backdrop/ESC
                    modal._config.backdrop = true;
                    modal._config.keyboard = true;
                    modal._init(); // Actualizar configuración
                }
            });
        });
    </script>

    <!-- Gestión de Modal de Eliminación -->
    <script>
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        $(document).on("click", ".btnDeleteTrabajador", function () {
            // Asignación de la acción al Form
            const id = this.dataset.id;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/actores/usuario/${id}`;

            // Para asignaciones subordinadas a si está Activo o Desactivo
            const activo = this.dataset.activo === '1';

            // Título y pregunta
            const titulo = document.getElementById('confirmDeleteLabel');
            titulo.innerText = `Confirmar ${activo ? 'Desactivación' : 'Reactivación'}`;
            const pregunta = document.querySelector('#confirmDeleteModal .modal-body p.fs-5');
            pregunta.innerText = `¿Está seguro que desea ${activo ? 'desactivar' : 'reactivar'} este usuario?`;

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
