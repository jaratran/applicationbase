@extends('layouts.app')

@section('head-scripts')
    <!-- DataTables + Bootstrap 5 (CDN o local) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Buttons (botones para exportar y más) -->
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS para Sincronizar scrolls en DataTable y estilos para botones-toggle en filtros -->
    <link rel="stylesheet" href="{{ asset('css/scrolls-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-dataTables.css') }}">

    <!-- Animar el ícono de la modal de espera de emisión de Programa Diario con rotación suave -->
	<style>
        @keyframes girar {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .icono-animado {
            animation: girar 2s linear infinite;
        }
	</style>

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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white fs-5">
                            Emisión de Programa Diario
                        </div>
                        <div class="card-body">
                            @include('includes.alertas-sistema')

                            <div class="row mb-3 pb-3">
                                <!-- Selector de Fecha -->
                                <div class="col-md-2">
                                    <label for="fecha_programa" class="form-label">Fecha del Programa Diario</label>
                                    <input type="date" class="form-control" id="fecha_programa" name="fecha_programa" value="{{ date('Y-m-d') }}">
                                </div>

                                <!-- Botón Reiniciar -->
                                <div class="col-md-2 d-none d-flex flex-column" id="contenedorBtnReiniciar">
                                    <button class="btn btn-secondary btnReiniciarPrograma align-self-start mt-auto" type="button">
                                        <i class="fa fa-sync-alt"></i> Cargar otra fecha
                                    </button>
                                </div>

                                <!-- Botón Ver Retiros -->
                                <div class="col-md-2 d-flex flex-column" id="contenedorBtnCargar">
                                    <button class="btn btn-secondary btnCargaDatosPrograma align-self-start mt-auto" type="button">
                                        <i class="fa fa-search"></i> Ver retiros planificados
                                    </button>
                                </div>

                                <!-- Botón Emitir Programa Diario (alineado a la derecha) -->
                                <div class="col-md-2 d-none d-flex flex-column" id="contenedorBtnEmite">
                                    <button class="btn btn-primary btnEmiteProgramaDiario align-self-start mt-auto" type="button">
                                        <i class="fa fa-bullhorn"></i> Emitir Programa Diario
                                    </button>
                                </div>
                            </div>

                            <!-- Resumen estadístico -->
                            <div id="resumen-estadistico-programa" class="mb-3 d-none">
                                <p class="mb-1">Total de Retiros Planificados: <strong id="total_retiros">0</strong></p>
                                <p class="mb-1 text-success">Nuevos: <strong id="total_nuevos">0</strong></p>
                                <p class="mb-1 text-warning">Actualizados: <strong id="total_actualizados">0</strong></p>
                                <p class="mb-1 text-muted">Sin Cambios: <strong id="total_sin_cambios">0</strong></p>
                            </div>

                            <div id="cardProgramaDiario" class="card mt-3 d-none">
                                <div class="card-header bg-secondary text-white fs-5">
                                    Programa Diario para fecha  : <strong id="fecha_vigente_programa"></strong>

                                    <div class="small fst-italic">
                                        <span id="version_programa_diario"></span>
                                    </div>
                                </div>

                                <div class="card-body">

                                    <!-- Tabla -->
                                    <div id="programaDiarioPlan" class="datatable-contenedor-externo">

                                        <!-- Controles ARRIBA -->
                                        <div class="row datatable-controles-superiores mb-2 gy-2">
                                            <!-- Filtros (izquierda) -->
                                            <div class="col-md-8">
                                                <div id="Estado" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                                    <label class="fw-bold mb-0">Filtrar por Estado:</label>

                                                    <div class="filtros-grid mt-2" role="group" aria-label="Filtro Estado">
                                                        <div class="col-4 col-sm-auto">
                                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado1" value="Planificado" autocomplete="off" checked>
                                                            <label class="btn filtro-btn-primary w-100" for="estado1">Planificado</label>
                                                        </div>
                                                        <div class="col-4 col-sm-auto">
                                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado2" value="En Proceso" autocomplete="off" checked>
                                                            <label class="btn filtro-btn-primary w-100" for="estado2">En Proceso</label>
                                                        </div>
                                                        <div class="col-4 col-sm-auto">
                                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado3" value="Efectuada" autocomplete="off" checked>
                                                            <label class="btn filtro-btn-primary w-100" for="estado3">Efectuada</label>
                                                        </div>
                                                        <div class="col-4 col-sm-auto">
                                                            <input type="checkbox" class="btn-check filtro-toggle" id="estado4" value="Cancelada" autocomplete="off" checked>
                                                            <label class="btn filtro-btn-primary w-100" for="estado4">Cancelada</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="Novedad" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                                    <label class="fw-bold mb-0">Filtrar por Novedad:</label>

                                                    <div class="filtros-grid mt-2" role="group" aria-label="Filtro Novedad">
                                                        <div class="col-4 col-sm-auto">
                                                            <input type="checkbox" class="btn-check filtro-toggle" id="novedad1" value="" autocomplete="off" checked>
                                                            <label class="btn filtro-btn-secondary w-100" for="novedad1">Sin Cambios</label>
                                                        </div>
                                                        <div class="col-4 col-sm-auto">
                                                            <input type="checkbox" class="btn-check filtro-toggle" id="novedad2" value="Nueva" autocomplete="off" checked>
                                                            <label class="btn filtro-btn-secondary w-100" for="novedad2">Nueva</label>
                                                        </div>
                                                        <div class="col-4 col-sm-auto">
                                                            <input type="checkbox" class="btn-check filtro-toggle" id="novedad3" value="Actualizada" autocomplete="off" checked>
                                                            <label class="btn filtro-btn-secondary w-100" for="novedad3">Actualizada</label>
                                                        </div>
                                                    </div>
                                                </div>

												<div id="Región" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
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
                                            <table class="table table-striped table-bordered listado">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Estado</th>
                                                        <th>Novedad</th>
                                                        <th>Sucursal</th>
														<th>Región</th>
                                                        <th>Procedencia</th>
                                                        <th>Proveedor</th>
                                                        <th>Fecha y hora Retiro</th>
                                                        <th>CAMIÓN</th>
                                                        <th>TK/BIN</th>
                                                        <th>Hora</th>
                                                        <th>ETA</th>
                                                        <th>KG EST</th>
                                                        <th>PRODUCT</th>
                                                        <th>ESPECIE</th>
                                                        <th>Carga Bins</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Filas dinámicas por AJAX -->
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

                            <!-- Formulario oculto para emisión -->
                            <form id="formEmisionProgramaDiario" method="POST" action="{{ route('programa-diario.efectuar-emision') }}">
                                @csrf
                                <input type="hidden" name="fecha_programa" id="fecha_programa_hidden">
                                <input type="hidden" name="programa_detalle" id="programa_detalle_hidden">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal para confirmar Emisión de Programa Diario -->
<div class="modal fade" id="confirmEmiteProgramaModal" tabindex="-1" aria-labelledby="confirmEmiteProgramaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmEmiteProgramaLabel">Confirmar Emisión de Programa Diario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center">
                <i class="fas fa-bullhorn text-primary mb-3" style="font-size: 4rem;"></i>
                <p class="fs-5">¿Confirma emisión del Programa Diario para la fecha seleccionada?</p>
                <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary btnConfirmaEmisionPrograma">
                    <i class="fa fa-paper-plane"></i> Emitir Programa
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal de Espera mientras se efectúa la Emisión en Proceso -->
<div class="modal fade" id="procesoEmisionProgramaModal" tabindex="-1" aria-labelledby="procesoEmisionProgramaLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="procesoEmisionProgramaLabel">Emisión de Programa Diario en Proceso</h5>
            </div>

            <div class="modal-body text-center">
                <i class="fas fa-hourglass-half text-info mb-3 icono-animado" style="font-size: 4rem;"></i>

                <p class="fs-5">
                    No cierre esta ventana ni recargue la página.
                </p>
                <p class="mb-1">
                    ⏳ Tiempo estimado: <span id="tiempoEstimadoSpan">calculando...</span> segundos.
                </p>
                <p class="mb-1 text-muted small">
                    Hora estimada de finalización: <span id="horaEstimadaSpan">calculando...</span>
                </p>
                <p class="text-muted small">
                    ⌛ Han transcurrido: <span id="cronometroEmision">0</span> segundos.
                </p>
            </div>

        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    @include('includes.constantes-js-catalogo')

    <!-- JS para DataTables + Bootstrap 5 -->
    <!-- JSZip para exportar a Excel - pdfMake para exportar a PDF -->
    <!-- Botones de DataTables -->
    <!-- Inicialización de dataTable - Scroll Horizontal y Filtros -->
	@include('includes.datatable-scripts')

	<!-- Bloque de Configuración de la dataTable -->
	<script>
        // La inicializamos después de pintarla en el JS de abajo
        // $(document).ready(function () {
        // 	inicializarDataTable('programaDiarioPlan');
        // });

		$(window).on('resize', function () {
			actualizarScrollHorizontal('programaDiarioPlan');
		});
	</script>

	<!-- Bloque de preparación y emisión de Programa Diario -->
    <script>
        function renderEstadoRetiro(estadoId) {
            switch (estadoId) {
                case ESTADO_RETIRO_ESPERANDO:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-clock text-warning fs-4" title="Esperando"></i>
                            <div>Esperando</div>
                        </td>`;
                case ESTADO_RETIRO_COMENTADO:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-comment-dots text-info fs-4" title="Comentada"></i>
                            <div>Comentado</div>
                        </td>`;
                case ESTADO_RETIRO_ACEPTADO:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-check-circle text-success fs-4" title="Aceptada"></i>
                            <div>Aceptado</div>
                        </td>`;
                case ESTADO_RETIRO_PLANIFICADO:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-calendar-check text-primary fs-4" title="Planificada"></i>
                            <div>Planificado</div>
                        </td>`;
                case ESTADO_RETIRO_PROGRAMADO:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-hourglass-half text-warning fs-4" title="En Proceso"></i>
                            <div>En Proceso</div>
                        </td>`;
                case ESTADO_RETIRO_TERMINADO:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-check-circle text-success fs-4" title="Efectuada"></i>
                            <div>Efectuada</div>
                        </td>`;
                case ESTADO_RETIRO_CANCELADO:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-times-circle text-danger fs-4" title="Cancelada"></i>
                            <div>Cancelada</div>
                        </td>`;
                default:
                    return `
                        <td class="text-center align-middle text-muted">
                            <i class="fas fa-question-circle text-muted fs-4" title="Desconocido"></i>
                            <div>Desconocido</div>
                        </td>`;
            }
        }

        function renderNovedadRetiro(novedad) {
            switch (novedad) {
                case 1:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-sync-alt text-info fs-4" title="Actualizada"></i>
                            <div>Actualizada</div>
                        </td>`;
                case 2:
                    return `
                        <td class="text-center align-middle">
                            <i class="fas fa-plus-circle text-primary fs-4" title="Nueva"></i>
                            <div>Nueva</div>
                        </td>`;
                case 0:
                default:
                    return `<td></td>`;
            }
        }

		function renderRegion(regionId) {
			switch (regionId) {
				case REGION_X:
					return 'X';

				case REGION_XII:
					return 'XII';

				default:
					return '—';
			}
		}

        // 👉 Actualizar la fecha visible en el header
        function formatearFecha(fechaISO) {
            const partes = fechaISO.split('-'); // [YYYY, MM, DD]
            return `${partes[2]}-${partes[1]}-${partes[0]}`;
        }

        // Formatea dateTime serializado a string a formato fecha/hora 'dd-mm-YYYY HH:ii'.
        function formatearFechaHora(datetime) {
            if (!datetime) return '';
            const fechaObj = new Date(datetime);
            const d = String(fechaObj.getDate()).padStart(2, '0');
            const m = String(fechaObj.getMonth() + 1).padStart(2, '0');
            const y = fechaObj.getFullYear();
            const h = String(fechaObj.getHours()).padStart(2, '0');
            const i = String(fechaObj.getMinutes()).padStart(2, '0');
            return `${d}-${m}-${y} ${h}:${i}`;
        }

        // Formatea dateTime serializado a string a formato hora/fecha 'HH:ii dd-mm-YYYY'.
        function formatearHoraFecha(datetime) {
            if (!datetime) return '';
            const fechaObj = new Date(datetime);
            const d = String(fechaObj.getDate()).padStart(2, '0');
            const m = String(fechaObj.getMonth() + 1).padStart(2, '0');
            const y = fechaObj.getFullYear();
            const h = String(fechaObj.getHours()).padStart(2, '0');
            const i = String(fechaObj.getMinutes()).padStart(2, '0');
            return `${h}:${i} ${d}-${m}-${y}`;
        }

        window.programaDetalle = [];                    // Variable global para mantener lo recibido desde el BACK con el AJAX

        // Obtener y preparar la visualización del Programa Diario a Emitir
        function cargarDatosEmision(fecha) {
            if (!fecha) return;

            $.ajax({
                url: '/programa-diario/previsualizar-emision',
                method: 'GET',
                data: { fecha: fecha },
                success: function (response) {
                    // Mostrar resumen
                    $('#total_retiros').text(response.total);
                    $('#total_nuevos').text(response.nuevos);
                    $('#total_actualizados').text(response.actualizados);
                    $('#total_sin_cambios').text(response.sin_cambios);
                    $('#resumen-estadistico-programa').removeClass('d-none');

                    // Mostrar tabla
                    if (response.detalles && response.detalles.length > 0) {

                        // De lo recibido sólo guardamos lo necesario para posterior retorno al BACK como emisión de ProgramaDiario (post revisón del usuario)
                        window.programaDetalle = response.detalles.map(item => ({
                                                                                    retiro_id: item.retiro_id,
                                                                                    estado: item.estado_retiro_id,
																					region_operativa_id: item.region_operativa_id,
                                                                                    sucursal_id: item.sucursal_id,
                                                                                    comuna_id: item.comuna_id,
                                                                                    proveedor_id: item.proveedor_id,
                                                                                    fecha_hora_retiro: item.fecha_hora_retiro, // sigue en formato string
                                                                                    camion_id: item.camion_id,
                                                                                    tipo_retiro_id: item.tipo_retiro_id,
                                                                                    duracion_viaje: item.duracion_viaje,
                                                                                    eta: item.eta,
                                                                                    kilogramos_estimados: item.kilogramos_estimados,
                                                                                    producto_id: item.producto_id,
                                                                                    especie_id: item.especie_id,
                                                                                    bins: item.bins,
                                                                                    novedad: item.novedad
                                                                                }));

                        const $tbody = $('.listado tbody');
                        $tbody.empty(); // Limpiar filas previas

                        response.detalles.forEach(item => {
                                                                const fila = `  <tr>
                                                                                    ${renderEstadoRetiro(item.estado_retiro_id)}
                                                                                    ${renderNovedadRetiro(item.novedad)}
                                                                                    <td>${item.sucursal}</td>
                                                                                    <td>${renderRegion(item.region_operativa_id)}</td>
                                                                                    <td>${item.comuna}</td>
                                                                                    <td>${item.proveedor}</td>
                                                                                    <td>${formatearFechaHora(item.fecha_hora_retiro)}</td>
                                                                                    <td>${item.camion}</td>
                                                                                    <td>${item.tipo_retiro}</td>
                                                                                    <td>${item.duracion_viaje}</td>
                                                                                    <td>${formatearHoraFecha(item.eta)}</td>
                                                                                    <td>${item.kilogramos_estimados}</td>
                                                                                    <td>${item.producto}</td>
                                                                                    <td>${item.especie}</td>
                                                                                    <td>${item.bins ?? '-'}</td>
                                                                                </tr>
                                                                            `;
                                                                $tbody.append(fila);
                                                            });

                        // 👇 Ahora inicializamos DataTable como en cualquier Blade con la información de la tabla pre-renderizada
                        inicializarDataTable('programaDiarioPlan');

                        // ✅ Al cargar los datos exitosamente
                        $('#fecha_vigente_programa').text(formatearFecha(fecha));
                        $('#version_programa_diario').text('Versión ' + (response.version ?? '1'));

                        $('#cardProgramaDiario').removeClass('d-none');                     // ← mostrar dataTable con sus filtros, scroll, paginadores y buscador
                        $('#contenedorBtnEmite').removeClass('d-none');                     // ← mostrar botón de emisión
                        $('#contenedorBtnReiniciar').removeClass('d-none');                 // ← mostrar botón de recargar página

                        $('#contenedorBtnCargar').addClass('d-none');                       // Ocultar botón original
                        $('#fecha_programa').prop('disabled', true);                        // Opcional: evitar cambiar fecha sin recargar
                    }
                },
                error: function (xhr) {
                    const mensaje = encodeURIComponent(xhr.responseJSON?.error || 'Error inesperado');
                    window.location.href = `${window.location.pathname}?error=${mensaje}`;
                }
            });
        }

        // Emisión del Programa Diario
        function emitirProgramaDiario() {
            if (!Array.isArray(window.programaDetalle)) {       // Si no existen datos para el programa diario
                console.warn('No hay datos en window.programaDetalle');     // Chaito nomás
                return;
            }

            // Obtenemos la fecha del Programa Diario
            const fecha = $('#fecha_programa').val();
            $('#fecha_programa_hidden').val(fecha);

            // Serializamos el contenido original desde lo que habíamos guardado
            $('#programa_detalle_hidden').val(JSON.stringify(window.programaDetalle));

            // Calcular tiempo estimado
            const segDelayCorreo = 2;
            const segDelayTelegram = 1;
            const factorTolereancia = 2;

            const cantidadRetiros = window.programaDetalle.length;
            const tiempoEstimado = cantidadRetiros * (segDelayCorreo + segDelayTelegram) * factorTolereancia; // o sea: 3 segundos por renglón * 2
            const horaActual = new Date();
            const milisegundosEstimados = tiempoEstimado * 1000;
            const horaEstimadaFin = new Date(horaActual.getTime() + milisegundosEstimados);

            // Cargar datos en la modal informativa
            $('#tiempoEstimadoSpan').text(tiempoEstimado);
            $('#horaEstimadaSpan').text(
                horaEstimadaFin.toLocaleTimeString('es-CL', {
                    hour: '2-digit',
                    minute: '2-digit'
                })
            );

            // Ocultamos modal de confirmación
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmEmiteProgramaModal'));
            if (modal) {
                modal.hide();
            }

            // Reiniciar cronómetro
            let segundosTranscurridos = 0;
            document.getElementById('cronometroEmision').innerText = segundosTranscurridos;

            // Iniciar intervalo
            window.intervaloCronometroEmision = setInterval(() => {
                segundosTranscurridos++;
                document.getElementById('cronometroEmision').innerText = segundosTranscurridos;
            }, 1000);

            // Mostrar modal de "proceso en curso"
            const modalProceso = new bootstrap.Modal(document.getElementById('procesoEmisionProgramaModal'));
            modalProceso.show();

            // Ejecutar el submit tradicional
            $('#formEmisionProgramaDiario').submit();
        }

        // ✅ Al hacer clic en "Ver retiros planificados"
        $('.btnCargaDatosPrograma').on('click', function () {
            const fecha = $('#fecha_programa').val();
            cargarDatosEmision(fecha);
        });

        // ▶️ Botón para iniciar emisión
        $('.btnEmiteProgramaDiario').on('click', function () {
            if (!Array.isArray(window.programaDetalle) || window.programaDetalle.length === 0) {
                alert('No hay datos para emitir el programa.');
                return;
            }
            $('#confirmEmiteProgramaModal').modal('show');
        });

        $('.btnConfirmaEmisionPrograma').on('click', function () {
            $('.btnConfirmaEmisionPrograma').prop('disabled', true);

            emitirProgramaDiario();
        });

        $('.btnReiniciarPrograma').on('click', function () {
            location.reload(); // Recarga toda la página
        });
    </script>
@endsection
