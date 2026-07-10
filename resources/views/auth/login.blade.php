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
                Iniciar Sesión
            </div>

            <div class="card-body">
                @include('includes.alertas-sistema')

                <form role="form" method="POST" action="{{ url('/login') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="tokenRole" value="{{Crypt::encrypt(1)}}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input id="password" type="password" class="form-control" name="password">
                            <button type="button" class="input-group-text" id="eyePassword" title="Mostrar contraseña">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="my-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-btn fa-sign-in"></i> Inicie Sesión
                        </button>
                    </div>

                    <div>
                        <a class="btn btn-link" href="{{ route('password.request') }}">¿Olvidó su Contraseña?</a>
                    </div>

                    <div class="mt-2">
                        <a href="https://onway.entelocean.com/Home/Index/es" target="_blank" class="text-secondary small text-decoration-none">
                            Ir a Onway <i class="fa fa-external-link-alt ms-1"></i>
                        </a>
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

@section('endbody-scripts')
<script>
    const togglePassword = document.getElementById('eyePassword');
    const password = document.getElementById('password');
  
    togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
</script>
@endsection
