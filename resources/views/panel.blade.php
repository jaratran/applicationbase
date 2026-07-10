@extends('layouts.app')

@php
    use App\Helpers\RenderEstadoNovedad;
@endphp

@section('head-scripts')
    <!-- DataTables + Bootstrap 5 (CDN o local) y DataTables Buttons (botones para exportar y más) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS para Sincronizar scrolls en DataTable y estilos para botones-toggle en filtros -->
    <link rel="stylesheet" href="{{ asset('css/filtros-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/scrolls-dataTables.css') }}">

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
                    Panel de Control
                </div>

                <div class="card-body">
                    @include('includes.alertas-sistema')

                    <div class="card">
                        <div class="card-header bg-secondary text-white fs-5">
                            Consolidado Semanal, desde el <strong>{{ $desdeFecha }}</strong> al <strong>{{ $hastaFecha }}</strong>
                        </div>

                        <div class="card-body">
                            <!-- Gráficos y KPIS Toneladas -->
                            <div class="row">
                                <!-- Gráfico Tons Plan x Sucursal y el % del Total - Hoy -->
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-white">
                                        <div class="card-header bg-primary text-white">
                                            Toneladas Plan x Sucursal y % del Total - Hoy
                                        </div>
                                        <div class="card-body text-center">
                                            <div id="divPrimerGrafico">
                                                <div class="ratio ratio-16x9">
                                                    <canvas id="barChart1"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- KPIS de Toneladas -->
                                <div class="col-md-2 mb-3">
                                    <div class="row pt-2">
                                        <div>
                                            <div class="card bg-secondary text-white">
                                                <div class="card-header">
                                                    Tons a Recibir ETA Hoy
                                                </div>
                                                <div class="card-body text-center">
                                                    <h2 class="text-center" id="kpiRcvrHoy">
                                                        {{ number_format($kpiRcvrHoy, 0, ',', '.') }}
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gráfico Tons Plan x día vs Tons Real x día - Últimos 7 días -->
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-white">
                                        <div class="card-header bg-primary text-white">
                                            Tons Plan x día vs Tons Real x día - Últimos 7 días
                                        </div>
                                        <div class="card-body text-center">
                                            <div id="divSegundoGrafico">

                                                <div class="ratio ratio-16x9">
                                                    <canvas id="barChart2"></canvas>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- KPIS de Toneladas -->
                                <div class="col-md-2">
                                    <div class="row pt-2">
                                        <div>
                                            <div class="card bg-success text-white">
                                                <div class="card-header">
                                                    Acum Plan Ults 7 días
                                                </div>
                                                <div class="card-body text-center">
                                                    <h2 class="text-center" id="kpiAcumPlan">
                                                        {{ number_format($kpiAcumPlan, 0, ',', '.') }}
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-3">
                                        <div>
                                            <div class="card bg-warning text-white">
                                                <div class="card-header">
                                                    Acum Real Ults 7 días
                                                </div>
                                                <div class="card-body text-center">
                                                    <h2 class="text-center" id="kpiAcumReal">
                                                        {{ number_format($kpiAcumReal, 0, ',', '.') }}
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-secondary text-white fs-5 d-flex justify-content-between align-items-center">
                            <!-- 🧭 Lado Izquierdo: Fecha y Versión -->
                            <div>
                                Programa Diario, vigente el día de HOY : <strong>{{ $fecha_vigente_programa }}</strong><br>
                                @if(!empty($version_programa_diario))
                                    <span class="small fst-italic">Versión {{ $version_programa_diario }}</span>
                                @endif
                            </div>

                            <!-- ⚖️ Lado Derecho: Total Kilos -->
                            <div class="text-end">
                                <span class="fw-bold">Total kilos considerados:</span><br>
                                <span class="fs-6">{{ number_format($totalKilosEstimados, 0, ',', '.') }} kg</span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="programaDiarioReal" class="datatable-contenedor-externo">
                                <!-- Controles ARRIBA -->
                                <div class="row datatable-controles-superiores mb-2 gy-2">
                                    <!-- Filtros (izquierda) -->
                                    <div class="col-12 col-md-8">
                                        <div id="Estado" class="filtro d-flex flex-wrap align-items-center gap-2 w-75">
                                            <label class="fw-bold mb-0">Filtrar por Estado:</label>

                                            <div class="filtros-grid mt-2" role="group" aria-label="Filtro Estado">
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
                                    <table id="tabla-version" class="table table-striped table-bordered listado">
                                        <thead>
                                            <tr class="text-center align-middle">
                                                <th>Estado</th>
                                                <th>Novedad</th>
                                                <th>Sucursal</th>
												<th>Región</th>
                                                <th>Procedencia</th>
                                                <th>Proveedor</th>
                                                <th>Fecha y hora Retiro</th>
                                                <th>Camión</th>
                                                <th>TK/BIN</th>
                                                <th>Hora</th>
                                                <th>ETA</th>
                                                <th>Kg. Est.</th>
                                                <th>Producto</th>
                                                <th>Especie</th>
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
            inicializarDataTable('programaDiarioReal');
        });

        $(window).on('resize', function () {
            actualizarScrollHorizontal('programaDiarioReal');
        });
	</script>

    <!-- Chart.js 3+ desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.responsive = true;

        // Función para obtener el valor de una variable CSS
        function getCssVar(varName) {
            return getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
        }

        // Obtener colores dinámicos
        const primaryColor = getCssVar('--bs-primary');
        const secondaryColor = getCssVar('--bs-secondary');
        const successColor = getCssVar('--bs-success');
        const warningColor = getCssVar('--bs-warning');
        const dangerColor = getCssVar('--bs-danger');
        const infoColor = getCssVar('--bs-info');
    </script>

    <!-- Gráfico 1 : Toneladas Plan x Sucursal y % del Total - Hoy -->
    <script>
        const datosGrafico1 = @json($tonsPorSucursal);

        const barData1 = {
            labels: datosGrafico1.labels,
            datasets: [
                {
                    label: 'Toneladas',
                    data: datosGrafico1.data,
                    backgroundColor: secondaryColor + 'b3', // Aproximación 70% opacidad
                    borderColor: secondaryColor,
                    borderWidth: 1
                }
            ]
        };
        const barConfig1 = {
            type: 'bar',
            data: barData1,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // 👈🏽 Esto oculta toda la leyenda (incluido el label del dataset)
                    },
                    title: {
                        display: true,
                        text: ''
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#000',
                        formatter: function(value, context) {
                            const dataArray = context.chart.data.datasets[0].data;
                            const total = dataArray.reduce((a, b) => a + b, 0);
                            const porcentaje = (value / total) * 100;
                            return porcentaje.toFixed(1) + '%';
                        },
                        font: {
                            weight: 'normal'
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 0
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        title: {
                            display: true,
                            text: 'Sucursales'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Toneladas'
                        }
                    }
                }
            },
            plugins: [ChartDataLabels] // Activar plugin de datalabels
        };
        // Renderización del gráfico 1
        const barChart1 = new Chart(document.getElementById('barChart1'), barConfig1);
        barChart1.update();
    </script>

    <!-- Gráfico 2 : Tons Plan x día vs Tons Real x día - Últimos 7 días -->
    <script>
        const datosGrafico2 = @json($planVsReal);

        const barData2 = {
            labels: datosGrafico2.labels,
            datasets: [
                {
                    label: 'Plan',
                    data: datosGrafico2.plan,
                    backgroundColor: successColor + 'b3', // Aproximación 70% opacidad
                    borderColor: successColor,
                    borderWidth: 1
                },
                {
                    label: 'Real',
                    data: datosGrafico2.real,
                    backgroundColor: warningColor + 'b3', // Aproximación 70% opacidad
                    borderColor: warningColor,
                    borderWidth: 1
                }
            ]
        };
        const barConfig2 = {
            type: 'bar',
            data: barData2,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: false,
                        text: ''
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        title: {
                            display: true,
                            text: 'Días'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Toneladas'
                        }
                    }
                }
            }
        };
        // Renderización del gráfico 2
        const barChart2 = new Chart(document.getElementById('barChart2'), barConfig2);
        barChart2.update();
    </script>
@endsection
