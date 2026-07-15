@php
    use Illuminate\Support\Facades\DB;

    $design = DB::table('design_parameters')->first();

    $logo = $design->logo_design ?? 'default_logo.png';

    $colorPrincipal   = $design->custom_primary ?? '#0d6efd';
    $colorSecundario  = $design->custom_secondary ?? '#6c757d';
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sitio en Mantención</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
        }

        .mantencion-box {
            background-color: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .mantencion-logo {
            max-height: 80px;
            margin-bottom: 1.5rem;
        }

        .titulo {
            color: {{ $colorPrincipal }};
        }

        .mensaje {
            color: {{ $colorSecundario }};
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 text-center">

    <div class="mantencion-box">
        <img src="{{ url('config/'.$logo) }}" alt="Logo" class="mantencion-logo">
        <h1 class="titulo">🛠 Sitio en Mantención</h1>
        <p class="mensaje mt-3">
            Estamos trabajando para brindarte una mejor experiencia.<br>
            Por favor vuelve a intentarlo en unos minutos.
        </p>
        <p class="text-muted mt-4 mb-0">Última actualización: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
