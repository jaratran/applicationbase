@extends('layouts.app')

@section('head-scripts')
    <!-- DataTables + Bootstrap 5 (CDN o local) y DataTables Buttons (botones para exportar y más) -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS para scrolls y filtros -->
    <link rel="stylesheet" href="{{ asset('css/scrolls-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros-dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-dataTables.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white fs-5">
                    Consolidado Semanal, desde el <strong>{{ $desdeFecha }}</strong> al <strong>{{ $hastaFecha }}</strong>
                </div>
                <div class="card-body">
                    @include('includes.alertas-sistema')

                    <div class="row">
                        <!-- Gráfico 1 -->
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header bg-primary text-white">Toneladas Plan x Sucursal y % del Total</div>
                                <div class="card-body">
                                    <div class="ratio ratio-16x9">
                                        <canvas id="barChart1"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KPI Tons ETA Hoy -->
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-header">Tons ETA Fecha</div>
                                <div class="card-body text-center">
                                    <h2 id="kpiRcvrHoy">{{ number_format($kpiRcvrHoy, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico 2 -->
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header bg-primary text-white">Plan vs Real — Últimos 7 días</div>
                                <div class="card-body">
                                    <div class="ratio ratio-16x9">
                                        <canvas id="barChart2"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KPIs Acumulados -->
                        <div class="col-md-2">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-header">Acum Plan 7 días</div>
                                <div class="card-body text-center">
                                    <h2 id="kpiAcumPlan">{{ number_format($kpiAcumPlan, 0, ',', '.') }}</h2>
                                </div>
                            </div>

                            <div class="card bg-warning text-white">
                                <div class="card-header">Acum Real 7 días</div>
                                <div class="card-body text-center">
                                    <h2 id="kpiAcumReal">{{ number_format($kpiAcumReal, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
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
@endsection

@section('endbody-scripts')
    <!-- Chart.js desde CDN + plugin datalabels -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        // Estilos de colores dinámicos
        function getCssVar(varName) {
            return getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
        }

        const primaryColor = getCssVar('--bs-primary');
        const secondaryColor = getCssVar('--bs-secondary');
        const successColor = getCssVar('--bs-success');
        const warningColor = getCssVar('--bs-warning');

        // 📊 Inyección de datos reales desde el backend
        const datosGrafico1 = @json($tonsPorSucursal);
        const datosGrafico2 = @json($planVsReal);
    </script>

    <!-- Gráfico 1 -->
    <script>
        const barChart1 = new Chart(document.getElementById('barChart1'), {
            type: 'bar',
            data: {
                labels: datosGrafico1.labels,
                datasets: [{
                    label: 'Toneladas',
                    data: datosGrafico1.data,
                    backgroundColor: secondaryColor + 'b3',
                    borderColor: secondaryColor,
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: (value, context) => {
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            return total ? (value / total * 100).toFixed(1) + '%' : '';
                        },
                        color: '#000'
                    }
                },
                scales: {
                    x: { title: { display: true, text: 'Sucursales' } },
                    y: { beginAtZero: true, title: { display: true, text: 'Toneladas' } }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>

    <!-- Gráfico 2 -->
    <script>
        const barChart2 = new Chart(document.getElementById('barChart2'), {
            type: 'bar',
            data: {
                labels: datosGrafico2.labels,
                datasets: [
                    {
                        label: 'Plan',
                        data: datosGrafico2.plan,
                        backgroundColor: successColor + 'b3',
                        borderColor: successColor,
                        borderWidth: 1
                    },
                    {
                        label: 'Real',
                        data: datosGrafico2.real,
                        backgroundColor: warningColor + 'b3',
                        borderColor: warningColor,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    x: { title: { display: true, text: 'Días' } },
                    y: { beginAtZero: true, title: { display: true, text: 'Toneladas' } }
                }
            }
        });
    </script>
@endsection
