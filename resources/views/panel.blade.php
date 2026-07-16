@extends('layouts.app')

@section('head-scripts')
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
@php
    // Estos valores estáticos muestran componentes del Panel; no representan métricas reales.
    $indicadores = [
        ['titulo' => 'Usuarios activos', 'valor' => '120', 'icono' => 'fa-users', 'color' => 'primary'],
        ['titulo' => 'Organizaciones registradas', 'valor' => '24', 'icono' => 'fa-building', 'color' => 'success'],
        ['titulo' => 'Unidades organizacionales', 'valor' => '48', 'icono' => 'fa-sitemap', 'color' => 'info'],
        ['titulo' => 'Configuraciones pendientes', 'valor' => '6', 'icono' => 'fa-sliders-h', 'color' => 'warning'],
    ];

    $actividades = [
        ['momento' => 'Hace unos minutos', 'descripcion' => 'Usuario de ejemplo creado', 'icono' => 'fa-user-plus'],
        ['momento' => 'Hoy', 'descripcion' => 'Perfil demostrativo actualizado', 'icono' => 'fa-user-edit'],
        ['momento' => 'Hoy', 'descripcion' => 'Configuración general modificada', 'icono' => 'fa-cog'],
        ['momento' => 'Ayer', 'descripcion' => 'Organización de ejemplo registrada', 'icono' => 'fa-building'],
        ['momento' => 'Ayer', 'descripcion' => 'Unidad organizacional actualizada', 'icono' => 'fa-sitemap'],
    ];

    $registros = [
        ['Elemento A', 'Usuario', 'Activo', 'Equipo general', 'Día 1'],
        ['Elemento B', 'Organización', 'Activo', 'Administración', 'Día 2'],
        ['Elemento C', 'Configuración', 'Pendiente', 'Soporte', 'Día 3'],
        ['Elemento D', 'Unidad', 'Activo', 'Coordinación', 'Día 4'],
        ['Elemento E', 'Perfil', 'Revisión', 'Equipo general', 'Día 5'],
        ['Elemento F', 'Organización', 'Inactivo', 'Administración', 'Día 6'],
    ];
@endphp

<div class="container py-3 py-lg-4">
    @include('includes.alertas-sistema')

    <div class="app-page-header">
        <div>
            <h1 class="app-page-title">Panel de Control</h1>
            <p class="text-muted mb-1">Bienvenido/a{{ Auth::user()->nombre_usuario ? ', '.Auth::user()->nombre_usuario : '' }}.</p>
            <p class="mb-0">Contenido demostrativo de los componentes disponibles para un Panel de Control.</p>
        </div>
        <div class="app-page-actions">
            <a href="{{ route('perfil.index') }}" class="btn btn-primary">
                <i class="fas fa-user-circle me-2" aria-hidden="true"></i>Ver mi perfil
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4" id="panel-kpis">
        @foreach ($indicadores as $indicador)
            <div class="col-12 col-sm-6 col-xl-3" data-panel-kpi>
                <div class="card panel-kpi-card border-{{ $indicador['color'] }} shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">{{ $indicador['titulo'] }}</div>
                            <div class="h3 mb-0">{{ $indicador['valor'] }}</div>
                        </div>
                        <div class="panel-kpi-icon rounded-circle bg-{{ $indicador['color'] }} bg-opacity-10 text-{{ $indicador['color'] }} d-flex align-items-center justify-content-center"
                            aria-hidden="true">
                            <i class="fas {{ $indicador['icono'] }} fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">Actividad mensual de ejemplo</div>
                <div class="card-body panel-chart"><canvas id="actividadMensualChart"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">Distribución de ejemplo por estado</div>
                <div class="card-body panel-chart"><canvas id="distribucionEstadosChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">Actividad reciente simulada</div>
                <div class="list-group list-group-flush">
                    @foreach ($actividades as $actividad)
                        <div class="list-group-item py-3">
                            <div class="d-flex gap-3">
                                <i class="fas {{ $actividad['icono'] }} text-primary mt-1" aria-hidden="true"></i>
                                <div>
                                    <div>{{ $actividad['descripcion'] }}</div>
                                    <small class="text-muted">{{ $actividad['momento'] }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">Registros de ejemplo</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="panelRegistrosTable" class="table table-striped table-bordered align-middle w-100">
                            <thead>
                                <tr><th>Elemento</th><th>Categoría</th><th>Estado</th><th>Responsable</th><th>Fecha</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($registros as $registro)
                                    <tr>
                                        @foreach ($registro as $valor)
                                            <td>{{ $valor }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        $(function () {
            const table = document.getElementById('panelRegistrosTable');

            if (table && !$.fn.DataTable.isDataTable(table)) {
                $(table).DataTable({
                    pageLength: 5,
                    lengthChange: false,
                    order: [],
                    language: { url: '/assets/languaje-DataTable/Spanish.json' }
                });
            }

            const styles = getComputedStyle(document.documentElement);
            const primary = styles.getPropertyValue('--bs-primary').trim();
            const success = styles.getPropertyValue('--bs-success').trim();
            const warning = styles.getPropertyValue('--bs-warning').trim();

            const activityCanvas = document.getElementById('actividadMensualChart');
            const statusCanvas = document.getElementById('distribucionEstadosChart');

            if (activityCanvas) {
                new Chart(activityCanvas, {
                    type: 'bar',
                    data: {
                        labels: ['Periodo 1', 'Periodo 2', 'Periodo 3', 'Periodo 4', 'Periodo 5', 'Periodo 6'],
                        datasets: [{ label: 'Actividad simulada', data: [10, 16, 12, 20, 15, 24], backgroundColor: primary, borderRadius: 5 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            }

            if (statusCanvas) {
                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Activo', 'Pendiente', 'En revisión'],
                        datasets: [{ data: [60, 25, 15], backgroundColor: [success, warning, primary], borderWidth: 2 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
                });
            }
        });
    </script>
@endsection
