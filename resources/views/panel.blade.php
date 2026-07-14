@extends('layouts.app')

@section('head-scripts')
    <!-- DataTables, integración con Bootstrap 5 y extensión Buttons para exportación -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- Estilos compartidos de DataTables: scroll sincronizado, filtros y colores configurables -->
    <link rel="stylesheet" href="{{ asset('css/scrolls-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-dataTables.css') }}">

    <!-- Estilos específicos del Panel: tarjetas KPI, gráficos y ajustes responsive -->
    <style>
        .panel-kpi-card { border: 0; border-left: .25rem solid var(--bs-primary); }
        .panel-kpi-card.border-success { border-left-color: var(--bs-success) !important; }
        .panel-kpi-card.border-warning { border-left-color: var(--bs-warning) !important; }
        .panel-kpi-card.border-info { border-left-color: var(--bs-info) !important; }
        .panel-kpi-icon { width: 2.75rem; height: 2.75rem; }
        .panel-chart { position: relative; min-height: 280px; }
        @media (max-width: 575.98px) { .panel-chart { min-height: 240px; } }

        /* Reduce el tamaño y espaciado del paginador de DataTables en pantallas angostas. */
        @media (max-width: 480px) {
            #panelActividadesTable #dt-paginate .pagination {
                --bs-pagination-padding-x: 0.50rem;
                --bs-pagination-font-size: 0.90rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="container py-3 py-lg-4">
    @include('includes.alertas-sistema')

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
        <div>
            <h1 class="h3 text-primary mb-1">Panel de Control</h1>
            <p class="text-muted mb-0">Vista demostrativa con información simulada.</p>
        </div>
        <span class="badge bg-secondary align-self-start align-self-sm-center">Datos de ejemplo</span>
    </div>

    <div class="row g-3 mb-4">
        @foreach ($indicadores as $indicador)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card panel-kpi-card border-{{ $indicador['color'] }} shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">{{ $indicador['titulo'] }}</div>
                            <div class="h3 mb-1">{{ $indicador['valor'] }}</div>
                            <span class="small text-{{ $indicador['variacion'][0] === '-' ? 'success' : 'primary' }}">{{ $indicador['variacion'] }} este mes</span>
                        </div>
                        <div class="panel-kpi-icon rounded-circle bg-{{ $indicador['color'] }} bg-opacity-10 text-{{ $indicador['color'] }} d-flex align-items-center justify-content-center">
                            <i class="fas {{ $indicador['icono'] }} fa-lg" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">Actividad mensual</div>
                <div class="card-body panel-chart"><canvas id="actividadMensualChart"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">Distribución por estado</div>
                <div class="card-body panel-chart"><canvas id="distribucionEstadosChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Últimas actividades</div>
        <div class="card-body">
            <div id="panelActividadesTable" class="datatable-contenedor-externo">
                <div class="row datatable-controles-superiores mb-2 gy-2">
                    <div class="col-12 col-md-8"></div>
                    <div class="col-12 col-md-4">
                        <div class="row g-2">
                            <div class="col-6"><div id="dt-length" class="w-100"></div></div>
                            <div class="col-6"><div id="dt-filter" class="w-100"></div></div>
                        </div>
                    </div>
                </div>

                <div class="top-scroll"></div>
                <div class="table-scroll">
                    <table class="table table-striped table-bordered table-hover align-middle listado d-none">
                    <thead>
                        <tr class="text-center align-middle"><th>Fecha</th><th>Actividad</th><th>Usuario</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($ultimasActividades as $actividad)
                            @php($estadoColor = ['Completado' => 'success', 'En curso' => 'primary', 'Pendiente' => 'warning'][$actividad['estado']])
                            <tr>
                                <td class="text-nowrap">{{ $actividad['fecha'] }}</td>
                                <td>{{ $actividad['actividad'] }}</td>
                                <td>{{ $actividad['usuario'] }}</td>
                                <td class="text-center"><span class="badge bg-{{ $estadoColor }}{{ $estadoColor === 'warning' ? ' text-dark' : '' }}">{{ $actividad['estado'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
                <div class="bottom-scroll"></div>

                <div class="row mb-2 gy-2 mt-2">
                    <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-start">
                        <div class="row g-2"><div id="dt-info" class="text-center"></div></div>
                    </div>
                    <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                        <div class="row g-2"><div id="dt-paginate"></div></div>
                    </div>
                </div>
                <div class="row mb-2 gy-2"><div id="dt-buttons" class="text-center mt-2"></div></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    @include('includes.datatable-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        $(document).ready(function () {
            inicializarDataTable('panelActividadesTable');
        });

        $(window).on('resize', function () {
            actualizarScrollHorizontal('panelActividadesTable');
        });

        const panelColors = {
            primary: getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim(),
            success: getComputedStyle(document.documentElement).getPropertyValue('--bs-success').trim(),
            warning: getComputedStyle(document.documentElement).getPropertyValue('--bs-warning').trim(),
            info: getComputedStyle(document.documentElement).getPropertyValue('--bs-info').trim()
        };

        new Chart(document.getElementById('actividadMensualChart'), {
            type: 'bar',
            data: { labels: @json($actividadMensual['etiquetas']), datasets: [{ label: 'Actividades', data: @json($actividadMensual['valores']), backgroundColor: panelColors.primary, borderRadius: 5 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        new Chart(document.getElementById('distribucionEstadosChart'), {
            type: 'doughnut',
            data: { labels: @json($distribucionEstados['etiquetas']), datasets: [{ data: @json($distribucionEstados['valores']), backgroundColor: [panelColors.success, panelColors.primary, panelColors.warning], borderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
        });
    </script>
@endsection
