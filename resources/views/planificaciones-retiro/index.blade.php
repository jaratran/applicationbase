@extends('layouts.app')

@section('head-scripts')
    <!-- DataTables + Bootstrap 5 (CDN o local) y DataTables Buttons (botones para exportar y más) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS para Sincronizar scrolls en DataTable y estilos para botones-toggle en filtros -->
    <link rel="stylesheet" href="{{ asset('css/filtros-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/scrolls-dataTables.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/headers-dataTables.css') }}"> --}}

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
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-12 col-xl-11">

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header bg-primary text-white fs-5">
							Ver Planificación de Retiros
						</div>
						<div class="card-body">
							@include('includes.alertas-sistema')

							<div id="planificacionesTable" class="datatable-contenedor-externo">
								<!-- Controles ARRIBA -->
								<div class="row datatable-controles-superiores mb-2 gy-2">
									<!-- Bloque de Filtros (izquierda) -->
									<div class="col-12 col-md-8">

										@if (isset($pivoteCorreo))		<!-- Si existe pivoteCorreo en la sesión es porque venimos desde el correo se muestra boton de liberación de pivote -->
											<div class="d-flex flex-wrap align-items-center gap-2 w-75">
												<form method="GET" action="{{ route('planificaciones-retiro.index') }}">
													<input type="hidden" name="chaoPivote" value="1">
													<button class="btn btn-primary" type="submit">
														<i class="fas fa-filter-slash"></i> Mostrar todos los retiros
													</button>
												</form>
											</div>

										@else 							<!-- De lo contrario se muestra filtro -->
											<div id="Estado" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
												<label class="fw-bold mb-0">Filtrar por Estado:</label>

												<div class="filtros-grid mt-2" role="group" aria-label="Filtro Estado">
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="estado-aceptada"  value="Aceptado"  autocomplete="off" checked>
														<label class="btn filtro-btn-primary w-100" for="estado-aceptada">Aceptado</label>
													</div>
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="estado-planificada" value="Planificado" autocomplete="off" checked>
														<label class="btn filtro-btn-primary w-100" for="estado-planificada">Planificado</label>
													</div>
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="estado-programada" value="Programado" autocomplete="off" checked>
														<label class="btn filtro-btn-primary w-100" for="estado-programada">Programado</label>
													</div>
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="estado-terminada" value="Terminado" autocomplete="off" checked>
														<label class="btn filtro-btn-primary w-100" for="estado-terminada">Terminado</label>
													</div>
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="estado-cancelada" value="Cancelado" autocomplete="off" checked>
														<label class="btn filtro-btn-primary w-100" for="estado-cancelada">Cancelado</label>
													</div>
												</div>
											</div>

											<div id="Tipo de Operación" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
												<label class="fw-bold mb-0">Filtrar por Operacion:</label>

												<div class="filtros-grid mt-2" role="group" aria-label="Filtro Operacion">
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="operacion-retiro"  value="Retiro"  autocomplete="off" checked>
														<label class="btn filtro-btn-info w-100" for="operacion-retiro">Retiro</label>
													</div>
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="operacion-reposicion"  value="Reposición"  autocomplete="off" checked>
														<label class="btn filtro-btn-info w-100" for="operacion-reposicion">Reposición</label>
													</div>
												</div>
											</div>

											<!-- El filtro por region sólo para roles que poseen más de una región operativa: X y XII
												- ROL_SOLICITANTE_PRODUCTOR
												- ROL_COORDINADOR
												- ROL_PERSONAL_GERENCIA
												- ROL_PERSONAL_PRODUCCION
												- ROL_PERSONAL_CALIDAD
												- ROL_PERSONAL_MANTENCION
												- ROL_PERSONAL_ROMANA
												- ROL_ADMINISTRADOR_IT
											 -->
											@if( count(Auth::user()->regiones_operativas_ids) > 1 )
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
											@endif
										@endif
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

								<!-- Para ajustar el valor de colspan y las columnas de la tabla según el rol del usuario autenticado -->
								@php
									$rolSesion = Auth::user()->rol_id;
									$esSolicitantePlanta    = $rolSesion === config('constantes.ROL_SOLICITANTE_PLANTA');
									$colspan = $esSolicitantePlanta ? 7 : 8;
								@endphp

								<!-- Tabla con scroll horizontal -->
								<div class="table-scroll">
									<table class="table table-striped table-bordered listado d-none">
										<thead>
											<tr class="table-light">
												<th class="text-center" colspan="1">Acciones</th> <!-- Acciones -->
												<th class="text-center" colspan="{{ $colspan }}">Retiro</th>
												<th class="text-center" colspan="5">Detalle</th>
												<th class="text-center" colspan="22">Planificación</th>
												<th class="text-center" colspan="1">Acciones</th> <!-- Acciones -->
											</tr>
											<tr>
												<th class="nosort text-center">Acciones</th>
												<th>Estado</th>							<!-- Cabeceras del Retiro -->
												<th>N° Operación Solicitada</th>
												<th>Tipo de Operación</th>

												@if (!$esSolicitantePlanta)
													<th>Solicitado por</th>				<!-- Si rol del usuario en sesión NO es Solicitante PLANTA debemos saber quien hizo la solicitud de retiro  -->
												@endif

												<th>Fecha y Hora de Solicitud</th>
												<th>Proveedor</th>
												<th>Planta</th>
												<th>Región</th>
												<th>Fecha y Hora de Retiro</th>
												<th>Tipo de Retiro</th>
												<th>Kg Estimados</th>
												<th>¿Reposición de Bins?</th>
												<th>Cant. Bins</th>

												<th>Fecha y Hora Planificada</th>		<!-- Cabeceras de Planificación -->
												<th>Horas/Días de Viaje a La Portada</th>
												<th>Fecha y Hora Estimada de Llegada</th>
												<th>Tipo de Materia Prima</th>
												<th>Especie</th>
												<th>¿Restricción?</th>

												<th>Tipo de Transporte</th>
												<th>Fecha de Embarque</th>
												<th>fecha de Arribo a Puerto</th>
												<th>Patente Camión</th>
												<th>Tipo de Camión</th>
												<th>Transportista</th>
												<th>Conductor</th>
												<th>Patente Rampla</th>
												<th>Estado Rampla</th>
												<th>Fecha rescate desde puerto</th>
												<th>Camión de rescate</th>
												<th>Conductor de rescate</th>

												<th>Motivo de última actualización</th>
												<th>Ticket de Cierre</th>

												<th>N° Retiro Solicitado</th>
												<th>Estado</th>							<!-- Cabeceras del Retiro -->
												<th class="nosort text-center">Acciones</th>
											</tr>
										</thead>
										<tbody>
											@foreach($retiros as $retiro)
												<tr>
													<!-- Acciones de dataTable Planificaciones -->
													<td>
														@include('planificaciones-retiro.partial-acciones-index')
													</td>
													<!-- Información del Retiro -->
													<td class="text-center align-middle">
														@include('planificaciones-retiro.partial-estados-index')
													</td>
													<td>{{ $retiro->id }}</td>

													@switch($retiro->tipo_operacion)
														@case(config('constantes.TIPO_OPERACION_RETIRO'))
															<td>Retiro</td>
															@break

														@case(config('constantes.TIPO_OPERACION_REPOSICION'))
															<td>Reposición</td>
															@break

														@default
															-
													@endswitch

													@if (!$esSolicitantePlanta)
														<td>{{ $retiro->solicitud->usuario->nombre_completo ?? '—' }}</td>		<!-- Si rol del usuario en sesión NO es Solicitante PLANTA debemos saber quien hizo la solicitud de retiro  -->
													@endif

													<td>{{ $retiro->created_at }}</td>

													<td>{{ $retiro->solicitud->maquila->empresa->razon_social ?? '—' }}</td>
													<td>{{ $retiro->solicitud->maquila->sucursal->nombre_sucursal ?? '—' }}</td>
													<td>{{ $retiro->solicitud->region_operativa_codigo }}</td>
													<td>{{ $retiro->fecha_retiro }}</td>
													<td>{{ $retiro->tipoRetiro->nombre ?? '—' }}</td>
													<td>{{ $retiro->kilogramos_estimados }}</td>
													<td>{{ $retiro->requiere_reposicion ? 'Sí' : 'No' }}</td>
													<td>{{ $retiro->cantidad_bins }}</td>

													<!-- Información de Planificación, solo si
													 		- NO no está CRUDA, VACIA, CERO
															- NO es tipo_operacion igual a REPOSICION -->
													@if ( $retiro->planificacion->estado_id == config('constantes.CATALOGO_NO_ESPECIFICADO') ||
													                $retiro->tipo_operacion == config('constantes.TIPO_OPERACION_REPOSICION') )

														@for ($i = 0; $i < 20; $i++)

															<!--  Si es el primer campo de una REPOSICION corresponde mostrar la fecha  -->
															@if ( ($i == 0) && ($retiro->tipo_operacion == config('constantes.TIPO_OPERACION_REPOSICION')) )
																<td>{{ $retiro->planificacion?->fecha_hora_planificada ?? '—' }}</td>
															@else
																<td>—</td>
															@endif

														@endfor

													@else
														<td>{{ $retiro->planificacion?->fecha_hora_planificada ?? '—' }}</td>
														<td>{{ $retiro->planificacion?->duracion ?? '—' }}</td>
														<td>{{ $retiro->planificacion?->fecha_hora_llegada ?? '—' }}</td>
														<td>{{ optional($retiro->planificacion?->tipoMateriaPrima)->nombre ?? '—' }}</td>
														<td>{{ optional($retiro->planificacion?->especie)->nombre ?? '—' }}</td>
														<td>{{ $retiro->planificacion?->tiene_restriccion ? 'Sí' : 'No' }}</td>

														<td>{{ optional($retiro->planificacion?->tipoTransporte)->nombre ?? '—' }}</td>
														<td>{{ $retiro->planificacion?->fecha_embarque ?? '—' }}</td>
														<td>{{ $retiro->planificacion?->fecha_arribo_puerto ?? '—' }}</td>
														<td>{{ optional($retiro->planificacion?->camion)->patente ?? '—' }}</td>
														<td>{{ optional($retiro->planificacion?->camion?->tipoCamion)->nombre ?? '—' }}</td>
														<td>{{ optional($retiro->planificacion?->camion?->empresa)->razon_social ?? '—' }}</td>
														<td>{{ optional($retiro->planificacion?->conductor)->nombre_completo ?? '—' }}</td>
														<td>{{ $retiro->planificacion?->patente_rampla }}</td>
														<td>{{ optional($retiro->planificacion?->estadoRampla)->nombre ?? '—' }}</td>
														<td>{{ $retiro->planificacion?->fecha_rescate_puerto ?? '—' }}</td>
														<td>{{ optional($retiro->planificacion?->camionRescate)->patente ?? '—' }}</td>
														<td>{{ optional($retiro->planificacion?->conductorRescate)->nombre_completo ?? '—' }}</td>

														<td>
															{{ optional($retiro->planificacion?->motivoModificacion)->id === 0
																? 'Sin Cambios'
																: (optional($retiro->planificacion?->motivoModificacion)->nombre ?? '—') }}
														</td>
														<td>{{ $retiro->planificacion?->ticket_cierre ?? '—' }}</td>
													@endif

													<td>{{ $retiro->id }}</td>
													<!-- Información del Retiro -->
													<td class="text-center align-middle">
														@include('planificaciones-retiro.partial-estados-index')
													</td>
													<!-- Acciones de dataTable Planificaciones -->
													<td>
														@include('planificaciones-retiro.partial-acciones-index')
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
	</div>
</div>

<!-- Modal: Examinar Planificación (SHOW) -->
<div class="modal fade" id="modalPlanificacion" tabindex="-1" aria-labelledby="modalPlanificacionLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable">
		<div class="modal-content">

			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="modalPlanificacionLabel">Detalle de la Planificacion de Retiro</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>

			<!-- Solo un modal-body -->
			<div class="modal-body" id="detailShowPlanificacion"></div>

			<div class="modal-footer">
				<a type="button" id="btnPrintPlanificacion" class="btn btn-primary" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
			</div>


		</div>
	</div>
</div>

<!-- Modal para cerrar planificación e ingresar TICKET -->
<div class="modal fade" id="cierraPlanificacionModal" tabindex="-1" aria-labelledby="cierraPlanificacionLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">

			<form method="POST" action="" id="formCierrePlanificacion">
				@csrf
				<input type="hidden" name="retiro_id" value="">

				<div class="modal-header bg-success text-white">
					<h5 class="modal-title" id="cierraPlanificacionLabel">Cerrar Planificación</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>

				<!-- Solo un modal-body -->
				<div class="modal-body text-center" id="detailCierrePlanificacion">
					<i class="fas fa-exclamation-triangle text-success mb-3" style="font-size: 4rem;"></i>

					<p class="fs-5">¿Está seguro que desea cerrar esta planificación?</p>
					<p class="text-muted small mb-3">Debe ingresar TICKET de cierre.</p>

					<div class="form-group px-3">
						<textarea name="ticket_cierre" id="ticket_cierre" class="form-control w-100" rows="4" placeholder="Ingrese el ticket de cierre de la planificación..." style="resize: vertical;"></textarea>
					</div>
				</div>

				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>

					<button type="button" class="btn btn-success" id="btnCerrar" disabled>
						<i class="fa fa-exclamation-circle"></i> Cerrar Planificación
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal para confirmar anulación de planificación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">

			<form method="POST" action="" id="formDeletePlanificacion">
				@csrf
				@method('DELETE')
				<input type="hidden" name="planificacion_id" id="planificacion_id">

				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title" id="confirmDeleteLabel">Anular Planificación</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>

				<div class="modal-body text-center">
					<i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 4rem;"></i>
					<p class="fs-5">¿Está seguro que desea anular esta planificación?</p>

					<div class="form-group w-100">
						<p class="text-muted small mb-3">Debe ingresar un comentario para la anulación.</p>

						<textarea name="comentario_anulacion"
								id="comentario_anulacion"
								class="form-control"
								rows="3"
								placeholder="Ingrese comentario"
								style="resize: vertical;"
								required></textarea>
					</div>
				</div>

				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>

					<button type="submit" class="btn btn-danger" id="btnAnular">
						<i class="fa fa-trash"></i> Anular Planificación
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('endbody-scripts')
    <!-- Incuimos la definición de constantes usadas en los Select2 para bifurcar por tipo de retiros en el HandleBars -->
    @include('includes.constantes-js-catalogo')

	<!-- JS para DataTables + Bootstrap 5 -->
    <!-- JSZip para exportar a Excel - pdfMake para exportar a PDF -->
    <!-- Botones de DataTables -->
    <!-- Inicialización de dataTable - Scroll Horizontal y Filtros -->
	@include('includes.datatable-scripts')
	<script>
		$(document).ready(function () {
			inicializarDataTable('planificacionesTable');
		});

		$(window).on('resize', function () {
			actualizarScrollHorizontal('planificacionesTable');
		});
	</script>

	<script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.min.js"></script>
    @include('planificaciones-retiro.template-handlebars-region-x-show')
    @include('planificaciones-retiro.template-handlebars-region-xii-show')
	<!-- Bloque de modal de SHOW -->
	<script>
		const modalPlanificacion = new bootstrap.Modal(document.getElementById('modalPlanificacion'));
		const source_x     = document.getElementById("template-region-x-show-planificacion").innerHTML;
		const template_x   = Handlebars.compile(source_x);
		const source_xii   = document.getElementById("template-region-xii-show-planificacion").innerHTML;
		const template_xii = Handlebars.compile(source_xii);
		let html;

		$(document).on("click", ".btnShowPlanificacion", function () {
			const id = $(this).data("id");
			const regOperativaId = $(this).data("reg-operativa-id");

			$.get(`/planificaciones-retiro/${id}`, function (data) {
				data.esBin            = parseInt(data.tipo_retiro_id) === TIPO_RETIRO_BINS;			// Marcamos si el Tipo de Retiro es BIN para manipular en HandleBar
				data.noEstadoAceptado = parseInt(data.estado_id)      !== ESTADO_RETIRO_ACEPTADO;	// Marcamos si el Estado del Retiro es ACEPTADO para manipular en HandleBar
				data.esCancelado      = parseInt(data.estado_id)      === ESTADO_RETIRO_CANCELADO;	// Marcamos si el Estado del Retiro es CANCELADO para manipular en HandleBar
				data.esRetiro         = parseInt(data.tipo_operacion) === TIPO_OPERACION_RETIRO;	// Marcamos si el Estado del Retiro es CANCELADO para manipular en HandleBar

				// Marcamos si el transporte desde REGION XII a REGION X involucra cabotaje y/o rescate
				data.conCabotaje = [TIPO_TRANSPORTE_BARCAZA, TIPO_TRANSPORTE_COMBINADO].includes(parseInt(data.planificacion?.tipo_transporte_id));
				data.conRescate = parseInt(data.planificacion?.tipo_transporte_id) === TIPO_TRANSPORTE_COMBINADO;

				switch(regOperativaId){
					case REGION_X:
						html = template_x(data);
						break;

					case REGION_XII:
						html = template_xii(data);
						break;

					default:
						return;
				}

				$("#detailShowPlanificacion").html(html);
				modalPlanificacion.show();
			});
		});
	</script>

	<!-- Bloque de modal de IMPRIMIR -->
	<script>
		document.getElementById("btnPrintPlanificacion").addEventListener("click", function (e) {
			e.preventDefault();
			const emblemaURL = "{{ url('/config/'.$designParameter['emblema_design']) }}";
			const contenido = document.getElementById("detailShowPlanificacion").innerHTML;
			const ventana = window.open("", "_blank", "width=800,height=600");

			ventana.document.write(`
				<html>
					<head>
						<title>Imprimir Detalle de la Planificación de Retiro</title>
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
						<h3 class="mb-4 text-center">Detalle de la Planificación de Retiro</h3>
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

	<!-- Bloque de modal para cerrar la planificación con ingreso de TICKET -->
	<script>
		const cierraPlanificacionModal = new bootstrap.Modal(document.getElementById("cierraPlanificacionModal"));
		$(document).on("click", ".btnCierrePlanificacion", function () {
			const planificacionId = $(this).data('id');

			// Actualizamos el form con la ruta de COMENTARIO
			$('#formCierrePlanificacion').attr('action', `/planificaciones-retiro/${planificacionId}/cerrar`);

			// Limpiar el campo y desactivar el botón por si quedó sucio de antes
			$('#ticket_cierre').val('');
			$('#btnCerrar').prop('disabled', true);

			cierraPlanificacionModal.show();
		});

		$(document).on('input', '#ticket_cierre', function () {
			const texto = $(this).val()?.trim();
			$('#btnCerrar').prop('disabled', texto.length === 0);
		});

		$(document).on("click", "#btnCerrar", function () {
			$('#formCierrePlanificacion').submit();
		});
	</script>

	<!-- Bloque de modal para confirmar DELETE eliminación de la planificación -->
	<script>
		const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));

		$(document).on("click", ".btnDeletePlanificacion", function () {
			const planificacionId = $(this).data('id');

			if (!planificacionId || planificacionId === 0) return;

			// Configuramos el formulario
			$('#formDeletePlanificacion').attr('action', `/planificaciones-retiro/${planificacionId}`);
			$('#planificacion_id').val(planificacionId);

			confirmDeleteModal.show();
		});

		// Captura del Submit para colocar Spinner en el Botón y evitar doble Submit
		$('#formDeletePlanificacion').on('submit', function (e) {
			// 🔁 Controlar doble submit
			// Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
			if (this.enviado) {
				console.log("⛔ Doble submit detectado, se detiene.");
				e.preventDefault();
				return;
			}

			const $btnAnular = $('#btnAnular');
			if ($btnAnular.length) {
				// Deshabilita el botón para evitar segundo click
				$btnAnular.prop('disabled', true);
				$btnAnular.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
			}

			// 🧷 Flag para evitar siguiente submit
			this.enviado = true;
		});
	</script>
@endsection
