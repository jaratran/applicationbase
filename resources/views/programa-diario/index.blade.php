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
                            Programas Diarios Emitidos
                        </div>
                        <div class="card-body">
                            @include('includes.alertas-sistema')

                            <!-- Tabla -->
                            <div id="programaDiarioTable" class="datatable-contenedor-externo">

                                <!-- Controles ARRIBA -->
                                <div class="row datatable-controles-superiores mb-2">
                                    <!-- Controles DataTable (derecha) -->
                                    <div class="col-12 col-md-4 ms-md-auto">
                                        <div class="row g-2 justify-content-end">
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
                                            <tr class="text-center align-middle">
                                                <th>Fecha</th>
                                                <th>Versiones</th>
                                                <th>Primera Emisión</th>
                                                <th>Última Emisión</th>
                                                <th>Nuevos</th>
                                                <th>Actualizados</th>
                                                <th>Sin cambio</th>
                                                <th>En proceso</th>
                                                <th>Efectuados</th>
                                                <th>Cancelados</th>
                                                <th>ETA Min</th>
                                                <th>ETA Max</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($programas as $programa)
                                                <tr class="text-center">
                                                    <td>{{ \Carbon\Carbon::parse($programa['fecha_programa'])->format('d-m-Y') }}</td>
                                                    <td>{{ $programa['cantidad_versiones'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($programa['primera_emision'])->format('d-m-Y H:i') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($programa['ultima_emision'])->format('d-m-Y H:i') }}</td>
                                                    <td>{{ $programa['retiros_nuevos'] }}</td>
                                                    <td>{{ $programa['retiros_actualizados'] }}</td>
                                                    <td>{{ $programa['retiros_sin_cambios'] }}</td>
                                                    <td>{{ $programa['retiros_en_proceso'] }}</td>
                                                    <td>{{ $programa['retiros_efectuados'] }}</td>
                                                    <td>{{ $programa['retiros_cancelados'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($programa['eta_minima'])->format('d-m-Y H:i') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($programa['eta_maxima'])->format('d-m-Y H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('programa-diario.ver', ['fecha' => \Carbon\Carbon::parse($programa['fecha_programa'])->format('Y-m-d')]) }}"
                                                            class="btn btn-info btn-xs text-white"
                                                            title="Ver versiones emitidas para esta fecha">
                                                            <i class="fa fa-eye"></i> Ver versiones
                                                        </a>

                                                        <a href="{{ route('programa-diario.consolidados', ['fecha' => \Carbon\Carbon::parse($programa['fecha_programa'])->format('Y-m-d')]) }}"
                                                            class="btn btn-secondary btn-xs"
                                                            title="Ver Consolidados para esta fecha">
                                                            <i class="fas fa-chart-bar"></i> Consolidados
                                                        </a>
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
@endsection

@section('endbody-scripts')
    <!-- JS para DataTables + Bootstrap 5 -->
    <!-- JSZip para exportar a Excel - pdfMake para exportar a PDF -->
    <!-- Botones de DataTables -->
    <!-- Inicialización de dataTable - Scroll Horizontal y Filtros -->
	@include('includes.datatable-scripts')

	<!-- Bloque de Configuración de la dataTable -->
	<script>
        $(document).ready(function () {
        	inicializarDataTable('programaDiarioTable');
        });

		$(window).on('resize', function () {
			actualizarScrollHorizontal('programaDiarioTable');
		});
	</script>
@endsection