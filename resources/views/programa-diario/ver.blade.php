@extends('layouts.app')

@php
    use App\Helpers\RenderEstadoNovedad;
@endphp

@section('head-scripts')
    <!-- DataTables + Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
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
                            Detalle de Versiones — Programa Diario Emitido  {{ \Carbon\Carbon::parse($fecha_programa)->format('d-m-Y') }}
                        </div>
                        <div class="card-body">
                            @include('includes.alertas-sistema')

                            <!-- Tabs de versiones -->
                            <ul class="nav nav-tabs mb-3" id="versionTabs" role="tablist">
                                @foreach ($detallesPorVersion as $version => $detalles)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link @if ($loop->first) active @endif"
                                                id="tab-v{{ $version }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#contenido-v{{ $version }}"
                                                type="button"
                                                role="tab"
                                                aria-controls="contenido-v{{ $version }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            Versión {{ $version }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Contenido de cada tab -->
                            <div class="tab-content" id="versionTabsContent">
                                @foreach ($detallesPorVersion as $version => $detalles)
                                    <div class="tab-pane fade @if ($loop->first) show active @endif"
                                        id="contenido-v{{ $version }}"
                                        role="tabpanel"
                                        aria-labelledby="tab-v{{ $version }}">

                                        <div id="contenedor-version-{{ $version }}" class="datatable-contenedor-externo mt-4">

                                            <!-- Controles ARRIBA -->
                                            <div class="row datatable-controles-superiores mb-2 gy-2">
                                                <!-- Filtros (izquierda) -->
                                                <div class="col-12 col-md-8">
                                                    <div id="Estado-v{{ $version }}" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                                        <label class="fw-bold mb-0">Filtrar por Estado:</label>

                                                        <div class="filtros-grid mt-2" role="group" aria-label="Filtro Estado">
                                                            <div class="col-4 col-sm-auto">
                                                                <input type="checkbox" class="btn-check filtro-toggle" id="estado2-v{{ $version }}" value="En Proceso" autocomplete="off" checked>
                                                                <label class="btn filtro-btn-primary w-100" for="estado2-v{{ $version }}">En Proceso</label>
                                                            </div>
                                                            <div class="col-4 col-sm-auto">
                                                                <input type="checkbox" class="btn-check filtro-toggle" id="estado3-v{{ $version }}" value="Efectuada" autocomplete="off" checked>
                                                                <label class="btn filtro-btn-primary w-100" for="estado3-v{{ $version }}">Efectuada</label>
                                                            </div>
                                                            <div class="col-4 col-sm-auto">
                                                                <input type="checkbox" class="btn-check filtro-toggle" id="estado4-v{{ $version }}" value="Cancelada" autocomplete="off" checked>
                                                                <label class="btn filtro-btn-primary w-100" for="estado4-v{{ $version }}">Cancelada</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="Novedad-v{{ $version }}" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                                        <label class="fw-bold mb-0">Filtrar por Novedad:</label>

                                                        <div class="filtros-grid mt-2" role="group" aria-label="Filtro Novedad">
                                                            <div class="col-4 col-sm-auto">
                                                                <input type="checkbox" class="btn-check filtro-toggle" id="novedad1-v{{ $version }}" value="" autocomplete="off" checked>
                                                                <label class="btn filtro-btn-secondary w-100" for="novedad1-v{{ $version }}">Sin Cambios</label>
                                                            </div>
                                                            <div class="col-4 col-sm-auto">
                                                                <input type="checkbox" class="btn-check filtro-toggle" id="novedad2-v{{ $version }}" value="Nueva" autocomplete="off" checked>
                                                                <label class="btn filtro-btn-secondary w-100" for="novedad2-v{{ $version }}">Nueva</label>
                                                            </div>
                                                            <div class="col-4 col-sm-auto">
                                                                <input type="checkbox" class="btn-check filtro-toggle" id="novedad3-v{{ $version }}" value="Actualizada" autocomplete="off" checked>
                                                                <label class="btn filtro-btn-secondary w-100" for="novedad3-v{{ $version }}">Actualizada</label>
                                                            </div>
                                                        </div>
                                                    </div>

													<div id="Región-v{{ $version }}" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
														<label class="fw-bold mb-0">Filtrar por Región Operativa:</label>

														<div class="filtros-grid mt-2" role="group" aria-label="Filtro Región">
															<div class="col-4 col-sm-auto">
																<input type="checkbox" class="btn-check filtro-toggle" id="Region1-v{{ $version }}" value="X" autocomplete="off" checked>
																<label class="btn filtro-btn-secondary w-100" for="Region1-v{{ $version }}">X Región</label>
															</div>
															<div class="col-4 col-sm-auto">
																<input type="checkbox" class="btn-check filtro-toggle" id="Region2-v{{ $version }}" value="XII" autocomplete="off" checked>
																<label class="btn filtro-btn-secondary w-100" for="Region2-v{{ $version }}">XII Región</label>
															</div>
														</div>
													</div>
												</div>

                                                <!-- Controles DataTable (derecha) -->
                                                <div class="col-12 col-md-4">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div id="dt-length-v{{ $version }}" class="w-100"></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div id="dt-filter-v{{ $version }}" class="w-100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- SCROLL SUPERIOR -->
                                            <div class="top-scroll"></div>

                                            <!-- Tabla con scroll horizontal -->
                                            <div class="table-scroll">
                                                <table id="tabla-version-{{ $version }}" class="table table-striped table-bordered listado">
                                                    <thead>
                                                        <tr class="text-center align-middle">
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
                                                        @foreach ($detalles as $detalle)
                                                            <tr class="text-center align-middle">
                                                                <td class="text-center align-middle">
                                                                    {!! RenderEstadoNovedad::renderEstadoIcono($detalle['estado']) !!}
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    {!! RenderEstadoNovedad::renderNovedadIcono($detalle['novedad']) !!}
                                                                </td>
                                                                <td>{{ $detalle['sucursal'] }}</td>
                                                                <td>{{ $detalle['region_operativa_id'] == config('constantes.REGION_XII') ? 'XII' : 'X' }}</td>
                                                                <td>{{ $detalle['procedencia'] }}</td>
                                                                <td>{{ $detalle['proveedor'] }}</td>
                                                                <td>{{ $detalle['fecha_hora_retiro'] }}</td>
                                                                <td>{{ $detalle['camion'] }}</td>
                                                                <td>{{ $detalle['tipo_retiro'] }}</td>
                                                                <td>{{ $detalle['duracion_viaje'] }}</td>
                                                                <td>{{ $detalle['eta'] }}</td>
                                                                <td>{{ $detalle['kg_estimados'] }}</td>
                                                                <td>{{ $detalle['producto'] }}</td>
                                                                <td>{{ $detalle['especie'] }}</td>
                                                                <td>{{ $detalle['bins'] ?? '-'}}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Scroll inferior -->
                                            <div class="bottom-scroll"></div>

                                            <!-- Controles ABAJO -->
                                            <div class="row mb-2 gy-2 mt-2">
                                                <!-- Bloque de Cantidad de Registros Mostrados (izquierda ventana amplia - centro ventana estrecha) -->
                                                <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-start">
                                                    <div class="row g-2">
                                                        <div id="dt-info-v{{ $version }}" class="text-center"></div>
                                                    </div>
                                                </div>

                                                <!-- Bloque de Paginador del DataTable (derecha ventana amplia - centro ventana estrecha) -->
                                                <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                                                    <div class="row g-2">
                                                        <div id="dt-paginate-v{{ $version }}"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Botones de Excel e Impresion/PDF -->
                                            <div class="row mb-2 gy-2">
                                                <div id="dt-buttons-v{{ $version }}" class="text-center mt-2"></div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="button" class="btn btn-secondary my-2 no-guard" onclick="window.location.href='{{ route('programa-diario.index') }}'">
                                <i class="fa fa-arrow-left"></i> Volver
                            </button>
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
            const inicializadas = {};

            function activateTable(contenedorID) {
                if (!inicializadas[contenedorID]) {
                    inicializarDataTable(contenedorID);
                    inicializadas[contenedorID] = true;
                }
            }

            // Tab inicial
            const $primero = $('div.tab-pane.show.active div[id^="contenedor-version-"]');
            if ($primero.length) activateTable($primero.attr('id'));

            // Cuando cambio de tab
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).data('bs-target');
                const $cont = $(target).find('div[id^="contenedor-version-"]');
                if ($cont.length) activateTable($cont.attr('id'));
            });
        });
    </script>
@endsection
