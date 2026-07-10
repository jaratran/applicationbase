<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        {{-- <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, user-scalable=0"/> --}}
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        {{-- CSRF Token para todas los AJAX vía $.ajax, $.post, etc. puedan incluir el token automáticamente. --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $designParameter['titulo_design'] }}</title>
        <link rel="icon" href="/config/{{ $designParameter['favicon_design'] }}">

        <!-- Bootstrap 5 CSS (estilos base y utilidades de diseño) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

        <!-- Fuente Inter desde Google Fonts (tipografía moderna y legible) -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

        <!-- Font Awesome 5.15.4 CON integrity CORRECTO -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <link rel="stylesheet" href="{{ asset('css/estilos-con-bootstrap-5.css') }}">

        @php
            function hexToRgb($hex) {
                $hex = str_replace('#', '', $hex);
                return implode(',', array_map('hexdec', str_split($hex, 2)));
            }
        @endphp
        <style>
            :root {
                --bs-primary: {{ $designParameter->custom_primary }};
                --bs-secondary: {{ $designParameter->custom_secondary }};
                --bs-success: {{ $designParameter->custom_success }};
                --bs-warning: {{ $designParameter->custom_warning }};
                --bs-danger: {{ $designParameter->custom_danger }};
                --bs-info: {{ $designParameter->custom_info }};

                /* === === Variables RGB asociadas a colores del sistema === ===
                 *  Estas variables permiten usar transparencias (con rgba(var(--bs-*-rgb), alpha))
                 *  útiles para fondos suaves o efectos visuales no sólidos.
                 *  Se deben mantener sincronizadas con los valores definidos en --bs-*
                */
                --bs-primary-rgb: {{ hexToRgb($designParameter->custom_primary) }};
                --bs-secondary-rgb: {{ hexToRgb($designParameter->custom_secondary) }};
                --bs-success-rgb: {{ hexToRgb($designParameter->custom_success) }};
                --bs-warning-rgb: {{ hexToRgb($designParameter->custom_warning) }};
                --bs-danger-rgb: {{ hexToRgb($designParameter->custom_danger) }};
                --bs-info-rgb: {{ hexToRgb($designParameter->custom_info) }};
            }
        </style>
        <link rel="stylesheet" href="{{ asset('css/sobrescribir-colores-bootstrap.css') }}">
    
        @if ( Auth::check() )
            <!-- Menú lateral colapsable y NavBar Menú CSS -->
            <link rel="stylesheet" href="{{ asset('css/menu-lateral-colapsable.css') }}">
            <link rel="stylesheet" href="{{ asset('css/navbar-menu.css') }}">
        @endif

        @yield('head-scripts')
    </head>

    <body id="app-layout" class="small d-flex flex-column min-vh-100">
        @if ( Auth::check() )
            <!-- Navbar -->
           @include('includes.navbar')

            <!-- Sidebar -->
            @include('includes.sidebar-menu')

            <!-- Overlay oscuro detrás del menú cuando está abierto -->
            <div id="sidebar-overlay"></div>            
        @endif

        <main class="flex-fill">
            @yield('content')
        </main>

        <footer></footer>
       
        @if ( Auth::check() )
            <!-- jQuery (necesario para Select2 y futuros plugins como DataTables) -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <!-- Bootstrap 5 JavaScript (componentes interactivos: modals, dropdowns, collapse, etc.) -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

            <!-- Menú lateral colapsable -->
            <script src="{{ asset('js/menu-lateral-colapsable.js') }}"></script>

            <!-- Para que todas los AJAX vía $.ajax, $.post, etc. incluyan el token automáticamente. -->
            <script>
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            </script>
        @endif

        @if ( Auth::check() )
            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endif

        @yield('endbody-scripts')    
    </body>
</html>
