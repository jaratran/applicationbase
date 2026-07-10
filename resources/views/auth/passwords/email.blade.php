@extends('layouts.app')

@section('head-scripts')
	<!-- CSS para Scrolls deshabilitados solo en login -->
    <style>
        html, body {
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
<div class="row min-vh-100 align-items-center">
    <!-- Columna del formulario -->
    <div class="col-md-4 p-4 text-center">
        <div class="card border-0 login-panel shadow rounded p-4">
            <div class="mb-3">
                <img src="{{ url('/config/'.$designParameter['emblema_design']) }}"
                     alt="Emblema del Sitio"
                     class="img-fluid"
                     style="max-height: 180px; object-fit: object-position: center;">
            </div>

            <div class="card-header bg-transparent border-0 fs-5 text-primary">
                Recuperar Contraseña
            </div>

            <div class="card-body">
                @include('includes.alertas-sistema')

                <form role="form" method="POST" action="{{ route('password.email') }}">
                    {{ csrf_field() }}

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row my-3">
                        <div class="col-8">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-btn fa-edit"></i> Envíe Correo de Recuperación
                            </button>
                        </div>
                        <div class="col-4">
                            <a class="btn btn-secondary w-100" href="{{ route('login') }}">
                                <i class="fa fa-btn fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Columna de la imagen -->
    <div class="col-md-8 d-none d-md-block">
        <div class="w-100 h-100 overflow-hidden d-flex align-items-center justify-content-center">
            <img src="{{ url('/config/'.$designParameter['fondo_pantalla_design']) }}"
                alt="Imagen decorativa"
                class="img-fluid w-100"
                style="object-fit: cover; object-position: center; height: 90vh; transform: translateY(-15%);">
        </div>
    </div>
</div>
@endsection
