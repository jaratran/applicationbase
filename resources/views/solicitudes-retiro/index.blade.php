@extends('layouts.app')

@section('head-scripts')
    <!-- DataTables + Bootstrap 5 (CDN o local) y DataTables Buttons (botones para exportar y más) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS para Sincronizar scrolls en DataTable y estilos para botones-toggle en filtros -->
    <link rel="stylesheet" href="{{ asset('css/filtros-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/scrolls-dataTables.css') }}">

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
		<div class="col-12 col-xl-9">

			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header bg-primary text-white fs-5">
							Ver Retiros de Materia Prima
						</div>
						<div class="card-body">
							@include('includes.alertas-sistema')

							<div id="retirosTable" class="datatable-contenedor-externo">
								<!-- Controles ARRIBA -->
								<div class="row datatable-controles-superiores mb-2 gy-2">
									<!-- Filtros (izquierda) -->
									<div class="col-12 col-md-8">

										@if (isset($pivoteCorreo))		<!-- Si existe pivoteCorreo en la sesión es porque venimos desde el correo se muestra boton de liberación de pivote -->
											<div class="d-flex flex-wrap align-items-center gap-2 w-75">
												<form method="GET" action="{{ route('solicitudes-retiro.index') }}">
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
														<input type="checkbox" class="btn-check filtro-toggle" id="estado-esperando" value="Esperando" autocomplete="off" checked>
														<label class="btn filtro-btn-primary w-100" for="estado-esperando">Esperando</label>
													</div>
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="estado-comentada" value="Comentado" autocomplete="off" checked>
														<label class="btn filtro-btn-primary w-100" for="estado-comentada">Comentado</label>
													</div>
													<div class="col-4 col-sm-auto">
														<input type="checkbox" class="btn-check filtro-toggle" id="estado-aceptada" value="Aceptado" autocomplete="off" checked>
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

								<!-- Para ajustar las columnas de la tabla según el rol del usuario autenticado -->
								@php
									$rolSesion = Auth::user()->rol_id;
									$esSolicitantePlanta    = $rolSesion === config('constantes.ROL_SOLICITANTE_PLANTA');
									$colspan = $esSolicitantePlanta ? 6 : 7;
								@endphp

								<!-- Tabla con scroll horizontal -->
								<div class="table-scroll">
									<table class="table table-striped table-bordered listado d-none">
										<thead>
											<tr>
												<th>Estado</th> <!-- Ojo que este titulo de columna identifica el campo del filtro en la sección 'Controles ARRIBA' -->
												<th>N° Retiro Solicitado</th>
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
												<th>Acciones</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($solicitudes as $solicitud)
												@foreach ($solicitud->retiros as $retiro)
													<tr>
														<td class="text-center align-middle">
															@switch($retiro->estado_id)
																@case(config('constantes.ESTADO_RETIRO_ESPERANDO'))
																	<i class="fas fa-clock text-warning fs-4" title="Esperando"></i>
																	<div>Esperando</div>
																	@break
																@case(config('constantes.ESTADO_RETIRO_COMENTADO'))
																	<i class="fas fa-comment-dots text-info fs-4" title="Comentada"></i>
																	<div>Comentado</div>
																	@break
																@case(config('constantes.ESTADO_RETIRO_ACEPTADO'))
																	<i class="fas fa-check-circle text-success fs-4" title="Aceptada"></i>
																	<div>Aceptado</div>
																	@break
																@case(config('constantes.ESTADO_RETIRO_PLANIFICADO'))
																	<i class="fas fa-calendar-check text-primary fs-4" title="Planificada"></i>
																	<div>Planificado</div>
																	@break
																@case(config('constantes.ESTADO_RETIRO_PROGRAMADO'))
																	<i class="fas fa-route text-primary fs-4" title="Programado"></i>
																	<div>Programado</div>
																	@break
																@case(config('constantes.ESTADO_RETIRO_TERMINADO'))
																	<i class="fas fa-flag-checkered text-secondary fs-4" title="Terminada"></i>
																	<div>Terminado</div>
																	@break
																@case(config('constantes.ESTADO_RETIRO_CANCELADO'))
																	<i class="fas fa-times-circle text-danger fs-4" title="Cancelada"></i>
																	<div>Cancelado</div>
																	@break
																@default
																	<i class="fas fa-question-circle text-danger fs-4" title="No Disponible"></i>
																	<div>No Disponible</div>
															@endswitch
														</td>

														<td data-order="{{ $retiro->id }}">#{{ $retiro->id }}</td>

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
															<td>{{ $solicitud->usuario->nombre_completo ?? '—' }}</td>		<!-- Si rol del usuario en sesión NO es Solicitante PLANTA debemos saber quien hizo la solicitud de retiro  -->
														@endif

														<td>{{ \Carbon\Carbon::parse($solicitud->created_at)->format('d-m-Y H:i') }}</td>

														<td>{{ $solicitud->maquila->empresa->razon_social ?? '—' }}</td>
														<td>{{ $solicitud->maquila->sucursal->nombre_sucursal ?? '—' }}</td>
														<td>{{ $solicitud->region_operativa_codigo }}</td>

														<td>{{ \Carbon\Carbon::parse($retiro->fecha_retiro)->format('d-m-Y H:i') }}</td>
														<td>{{ $retiro->tipoRetiro->nombre ?? '—' }}</td>
														<td>{{ number_format($retiro->kilogramos_estimados, 0, ',', '.') }} kg</td>

														<td>{{ $retiro->requiere_reposicion ? 'Sí' : 'No' }}</td>
														<td>{{ $retiro->cantidad_bins ?? '—' }}</td>
														<td>
															<div class="d-grid gap-1">
																<div class="d-flex justify-content-center gap-1">
																	<!-- Botón de EXAMINAR -->
																	<button class="btn btn-info btn-xs text-white btnShowRetiro" type="button" data-id="{{ $retiro->id }}">
																		<i class="fa fa-eye"></i>
																	</button>

																	<!-- Primero evaluamos si corresponde desplegar botón al rol del usuario -->
																	@if( in_array(Auth::user()->rol_id, [   config('constantes.ROL_SOLICITANTE_PLANTA'), config('constantes.ROL_SOLICITANTE_PLANTA_XII'),
																	                                        config('constantes.ROL_SOLICITANTE_PRODUCTOR'),
																	                                        config('constantes.ROL_COORDINADOR'), config('constantes.ROL_COORDINADOR_XII'),
																											config('constantes.ROL_ADMINISTRADOR_IT') ]) )

																		<!-- Luego verificamos que la solicitud haya sido creada por el usuario, quien es el dueño y es quien puede editarla -->
																		@if ( Auth::user()->id == $solicitud->usuario_id )

																			<!-- Botón de EDITAR solo si el retiro está en estado Esperando o Comentado -->
																			@if ( $retiro->estado_id == config('constantes.ESTADO_RETIRO_ESPERANDO') || $retiro->estado_id == config('constantes.ESTADO_RETIRO_COMENTADO') )
																				<a class="btn btn-warning btn-xs text-white" href="{{ route('solicitudes-retiro.edit', ['id' => Crypt::encrypt($retiro->id)]) }}">
																					<i class="fa fa-edit"></i>
																				</a>
																			@endif
																		@endif
																	@endif
																</div>

																<div class="d-flex justify-content-center gap-1">
																	<!-- Primero evaluamos si corresponde desplegar botón al rol del usuario -->
																	@if( in_array(Auth::user()->rol_id, [ 	config('constantes.ROL_COORDINADOR'), config('constantes.ROL_COORDINADOR_XII'),
																											config('constantes.ROL_ADMINISTRADOR_IT') ]) )

																		<!-- Botón de EVALUAR solo si el retiro está en estado Esperando o Comentado -->
																		@if ( $retiro->estado_id == config('constantes.ESTADO_RETIRO_ESPERANDO') || $retiro->estado_id == config('constantes.ESTADO_RETIRO_COMENTADO') )
																			<button class="btn btn-warning btn-xs text-white btnEvalRetiro" type="button" data-id="{{ $retiro->id }}">
																				<i class="fa fa-balance-scale"></i>
																			</button>
																		@endif
																	@endif

																	<!-- Primero evaluamos si corresponde desplegar botón al rol del usuario -->
																	@if( in_array(Auth::user()->rol_id, [   config('constantes.ROL_SOLICITANTE_PLANTA'), config('constantes.ROL_SOLICITANTE_PLANTA_XII'),
																	                                        config('constantes.ROL_SOLICITANTE_PRODUCTOR'),
																	                                        config('constantes.ROL_COORDINADOR'), config('constantes.ROL_COORDINADOR_XII'),
																											config('constantes.ROL_ADMINISTRADOR_IT') ]) )

																		<!-- Botón de ELIMINAR/cancelar solo si el retiro está en estado Esperando o Comentado -->
																		@if ( $retiro->estado_id == config('constantes.ESTADO_RETIRO_ESPERANDO') || $retiro->estado_id == config('constantes.ESTADO_RETIRO_COMENTADO') )
																			<button type="button" class="btn btn-danger btn-xs btnDeleteRetiro" data-id="{{ $retiro->id }}">
																				<i class="fas fa-trash"></i> {{-- eliminar/cancelar --}}
																			</button>
																		@endif
																	@endif
																</div>
															</div>
														</td>
													</tr>
												@endforeach
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

<!-- Modal: Examinar Retiro (SHOW) -->
<div class="modal fade" id="modalShowRetiro" tabindex="-1" aria-labelledby="modalShowRetiroLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="modalShowRetiroLabel">Detalle del Retiro de Materia Prima</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>

			<!-- Solo un modal-body -->
			<div class="modal-body" id="detailRetiro"></div>

			<div class="modal-footer">
				<a type="button" id="btnPrintShow" class="btn btn-primary btnPrintRetiro" target="_blank" data-id-modal-body="detailRetiro"><i class="fa fa-print"></i> Imprimir</a>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal: Evaluar Retiro -->
<div class="modal fade" id="modalEvalRetiro" tabindex="-1" aria-labelledby="modalEvalRetiroLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="modalEvalRetiroLabel">Detalle del Retiro de Materia Prima</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>

			<!-- Solo un modal-body -->
			<div class="modal-body" id="detailRetiro"></div>

			<div class="modal-footer d-flex justify-content-between align-items-center flex-wrap">
				<div class="d-flex flex-wrap gap-2">
					<form method="POST" action="{{ route('solicitudes-retiro.aprobar', ['id' => '__ID__']) }}" id="formEvaluacion">
						@csrf
						<button type="button" class="btn btn-success" id="btnAprobarRetiro">
							<i class="fa fa-check-circle"></i> Aprobar
						</button>
					</form>

					<button type="button" class="btn btn-danger" id="btnObservarRetiro" data-id="">
						<i class="fa fa-exclamation-circle"></i> Observar Retiro
					</button>
				</div>

				<div class="d-flex flex-wrap gap-2">
					<a type="button" id="btnPrintEval" class="btn btn-primary btnPrintRetiro" target="_blank" data-id-modal-body="detailEvalRetiro"><i class="fa fa-print"></i> Imprimir</a>

					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fa fa-times"></i> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal para confirmar eliiminación de Retiro -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<form id="deleteForm" method="POST">
				@csrf
				@method('DELETE')

				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title" id="confirmDeleteLabel">Confirmar Anulación</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>

				<div class="modal-body text-center">
					<i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 4rem;"></i>
					<p class="fs-5">¿Está seguro que desea anular este Retiro?</p>

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
					<button type="submit" class="btn btn-danger" id="btnAnular"> <!-- Botón programado abajo en el JS que lanza la Modal -->
						<i class="fa fa-ban"></i> Eliminar
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal para confirmar observación a retiro -->
<div class="modal fade" id="confirmObservacionModal" tabindex="-1" aria-labelledby="confirmObservacionLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">

			<form method="POST" action="{{ route('solicitudes-retiro.comentar', ['id' => '__ID__']) }}" id="formObservacion">
				@csrf

				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title" id="confirmObservacionLabel">Confirmación de Observación</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>

				<div class="modal-body text-center">
					<i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 4rem;"></i>

					<p class="fs-5">¿Desea marcar este retiro como observado?</p>
					<p class="text-muted small mb-3">Debe indicar el motivo de la observación antes de continuar.</p>

					<div class="form-group px-3">
						<textarea name="comentario" id="comentario" class="form-control w-100" rows="4" placeholder="Ingrese el motivo de la observación..." style="resize: vertical;"></textarea>
					</div>
				</div>

				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fa fa-times"></i> Cancelar
					</button>

					<button type="button" class="btn btn-danger" id="btnObservar" disabled>
						<i class="fa fa-exclamation-circle"></i> Observar
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

	<!-- Bloque de Configuración de la dataTable -->
	<script>
		$(document).ready(function () {
			inicializarDataTable('retirosTable');
		});

		$(window).on('resize', function () {
			actualizarScrollHorizontal('retirosTable');
		});
	</script>

	<!-- Bloque de IMPRESIÓN -->
	<script>
		document.querySelectorAll(".btnPrintRetiro").forEach(function(button) {
			button.addEventListener("click", function (e) {
				e.preventDefault();

				const emblemaURL = "{{ url('/config/'.$designParameter['emblema_design']) }}";
				const contenido = document.getElementById($(this).data("id-modal-body")).innerHTML;

				const ventana = window.open("", "_blank", "width=800,height=600");
				ventana.document.write(`
					<html>
						<head>
							<title>Imprimir Detalle del Retiro de Materia Prima</title>
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
							<h3 class="mb-4 text-center">Detalle del Retiro de Materia Prima</h3>
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
		});
	</script>

	<!-- Bloque de ELIMINACIÓN -->
    <script>
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        $(document).on("click", ".btnDeleteRetiro", function () {
			// Asignación de la acción de borrado al Form de la Modal
            const id = this.dataset.id;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/solicitudes-retiro/${id}`;

			// 🔄 Limpieza previa de campos hidden anteriores
			$('#deleteForm input[name="pivoteCorreo"]').remove();

			// 🔁 Insertamos campo oculto si hay pivote
			@if(session()->has('pivoteCorreo'))
				const inputPivote = document.createElement('input');
				inputPivote.type = 'hidden';
				inputPivote.name = 'pivoteCorreo';
				inputPivote.value = JSON.stringify(@json(session('pivoteCorreo')));
				deleteForm.appendChild(inputPivote);
			@endif

            confirmDeleteModal.show();
        });

		// Captura del Submit para colocar Spinner en el Botón y evitar doble Submit
		$('#deleteForm').on('submit', function (e) {
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

	<!-- Carga de definiciones de HandleBars y definición de función que serán usadas en bloque de EXAMINACIÓN y EVALUACION del retiro-->
	<script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.min.js"></script>

	<!-- Bloque multiproposito para EXAMINACIÓN y EVALUACION del retiro-->
	@include('solicitudes-retiro.template-handlebars-retiro-detalle')
	<script>
		const templateSource = document.getElementById("template-retiro-detalle").innerHTML;
		const templateRetiro = Handlebars.compile(templateSource);

		// Aplica listeners a los CHEVRONES (elementos de colapso) para mostrar/ocultar y actualizar íconos y textos
		function configurarToggle(modal, baseId) {
			const collapse = modal.querySelector(`#${baseId}Collapse`);
			const icon     = modal.querySelector(`#${baseId}IconToggle`);
			const text     = modal.querySelector(`#${baseId}TextToggle`);

			if (collapse && icon && text) {
				collapse.addEventListener('hide.bs.collapse', function () {
					icon.classList.remove('fa-chevron-up');
					icon.classList.add('fa-chevron-down');
					text.textContent = 'Mostrar';
				});

				collapse.addEventListener('show.bs.collapse', function () {
					icon.classList.remove('fa-chevron-down');
					icon.classList.add('fa-chevron-up');
					text.textContent = 'Ocultar';
				});
			}
		}

		// Resalta el fondo de aquellas celdas o campos del historial de cambio que fueron modificadas
		function resaltarCambios(modal, retiroActual, historial) {
			for (let i = 0; i < historial.length; i++) {
				const anterior = historial[i];
				const comparado = (i === 0) ? retiroActual : historial[i - 1];

				const fila = modal.querySelectorAll('#cambiosCollapse tbody tr')[i];
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
					switch (campo) {
						case 'requiere_reposicion':
							distinto = Boolean(valorAnt) !== Boolean(valorNuevo);
							break;

						case 'cantidad_bins':
							distinto = parseInt(valorAnt ?? 0) !== parseInt(valorNuevo ?? 0);
							break;

						default:
							distinto = String(valorAnt ?? '') !== String(valorNuevo ?? '');
							break;
					}

					if (distinto) {
						fila.children[index].classList.add('bg-cambio');
					}
				});
			}
		}

		// Incrusta data recibida del back al efectuar consulta por retiro que se esta examinando (show) o evaluando
		function cargarDetalleRetiro(modalElement, retiroId) {
			$.get(`/solicitudes-retiro/${retiroId}`, function (data) {
				data.esBin       = parseInt(data.tipo_retiro_id) === TIPO_RETIRO_BINS; // Marcamos si el Tipo de Retiro es BIN para manipular en HandleBar
				data.esCancelado = parseInt(data.estado_id)      === ESTADO_RETIRO_CANCELADO; // Marcamos si el estado del retiro es CANCELADO para manipular en HandleBar
				data.esRetiro    = parseInt(data.tipo_operacion) === TIPO_OPERACION_RETIRO;	// Marcamos si el Estado del Retiro es CANCELADO para manipular en HandleBar

				const html = templateRetiro(data); // Rellenamos el HandleBar con la data recibida

				modalElement.querySelector('#detailRetiro').innerHTML = html; // Cargamos en el HTML (DOM) el HandleBar con el detalle recibido desde el Back
				resaltarCambios(modalElement, data, data.historial); // 🔥 Comparar valores y resaltar cambios. Requiere que la data este incrustada en el HTML (DOM).

				['comentarios', 'cambios'].forEach(baseId => configurarToggle(modalElement, baseId)); // Actualizamos CHEVRONES de Comentarios e Historial : Collapse, iconToggle y textToggle

				new bootstrap.Modal(modalElement).show(); // Mostramos la modal que corresponde
			});
		}

		$(document).on("click", ".btnShowRetiro", function () {
			const retiroId = $(this).data("id");
			modalElement = document.getElementById('modalShowRetiro');
			cargarDetalleRetiro(modalElement, retiroId);
		});

		$(document).on("click", ".btnEvalRetiro", function () {
			const retiroId = $(this).data("id");
			modalElement = document.getElementById('modalEvalRetiro');

			$(modalElement).find('#formEvaluacion').attr('action', `/solicitudes-retiro/${retiroId}/aprobar`); // Actualizamos el form con la ruta de APROBACIÓN
			$(modalElement).find('#btnObservarRetiro').data('id', retiroId); // 💡 Pasamos el retiroId al botón de observar retiro.

			cargarDetalleRetiro(modalElement, retiroId);
		});
	</script>

	<!-- Bloque de COMENTARIO al retiro -->
	<script>
		const confirmObservacionModal = new bootstrap.Modal(document.getElementById("confirmObservacionModal"));
		$(document).on("click", "#btnObservarRetiro", function () {
			const retiroId = $(this).data('id');

			// Actualizamos el form con la ruta de COMENTARIO
			$('#formObservacion').attr('action', `/solicitudes-retiro/${retiroId}/comentar`);

			// Limpiar el campo y desactivar el botón por si quedó sucio de antes
			$('#comentario').val('');
			$('#btnObservar').prop('disabled', true);

			confirmObservacionModal.show();
		});

		$(document).on('input', '#comentario', function () {
			const texto = $(this).val()?.trim();
			$('#btnObservar').prop('disabled', texto.length === 0);
		});

		$(document).on("click", "#btnObservar", function () {
			$('#formObservacion').submit();
		});

		// Captura del Submit para colocar Spinner en el Botón y evitar doble Submit
		$('#formObservacion').on('submit', function (e) {
			// 🔁 Controlar doble submit
			// Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
			if (this.enviado) {
				console.log("⛔ Doble submit detectado, se detiene.");
				e.preventDefault();
				return;
			}

			const $btnObservar = $('#btnObservar');
			if ($btnObservar.length) {
				// Deshabilita el botón para evitar segundo click
				$btnObservar.prop('disabled', true);
				$btnObservar.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
			}

			// 🧷 Flag para evitar siguiente submit
			this.enviado = true;
		});
	</script>

	<!-- Bloque de APROBACIÓN al retiro -->
	<script>
		$(document).on("click", "#btnAprobarRetiro", function () {
			$('#formEvaluacion').submit();
		});

		// Captura del Submit para colocar Spinner en el Botón y evitar doble Submit
		$('#formEvaluacion').on('submit', function (e) {
			// 🔁 Controlar doble submit
			// Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
			if (this.enviado) {
				console.log("⛔ Doble submit detectado, se detiene.");
				e.preventDefault();
				return;
			}

			const $btnAprobarRetiro = $('#btnAprobarRetiro');
			if ($btnAprobarRetiro.length) {
				// Deshabilita el botón para evitar segundo click
				$btnAprobarRetiro.prop('disabled', true);
				$btnAprobarRetiro.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
			}

			// 🧷 Flag para evitar siguiente submit
			this.enviado = true;
		});
	</script>
@endsection
