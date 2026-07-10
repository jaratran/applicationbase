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
    <div class="row min-vh-100 align-items-center position-relative">
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
                    Actualizar Contraseña
                </div>

                <!-- Globo solo para móviles -->
                <div class="d-block d-md-none mt-3">
                    <div class="bg-white rounded shadow p-3 w-100 mx-auto" style="max-width: 390px;">
                        <h6 class="text-primary fw-bold mb-2">La contraseña debe ...</h6>

                        <div class="small text-muted text-center" style="line-height: 1.4;">
                            <p class="mt-3 mb-1">Tener mínimo ocho caracteres.</p>
                            <p class="mb-1">Contener al menos un número.</p>
                            <p>Contener al menos una mayúscula y una minúscula.</p>
                        </div>
                        
                    </div>
                </div>

                <div class="card-body">
                    @include('includes.alertas-sistema')

                    <form role="form" method="POST" action="{{ route('perfil.password.update') }}">
                        <input type="hidden" name="user" value="{{$id}}">
                        {{ csrf_field() }}

                        <div class="my-3 form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="control-label">Ingrese su nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" autofocus required>
                                <button type="button" class="input-group-text" id="eyePasswordNew" title="Mostrar contraseña">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="my-3 form-group{{ $errors->has('confirmPassword') ? ' has-error' : '' }}">
                            <label class="control-label">Confirme su nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                <button type="button" class="input-group-text" id="eyePasswordConfirm" title="Mostrar contraseña">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            @if ($errors->has('confirmPassword'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('confirmPassword') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="row my-3">
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-btn fa-edit"></i> Actualice Contraseña
                                </button>
                            </div>
                            <div class="col-4">
                                <a class="btn btn-secondary w-100" href="{{ route('perfil.index') }}">
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

        <!-- “globo” con condiciones de contraseña -->
        <div class="d-none d-md-block position-absolute" style="top: 53%; left: 35%; z-index: 10; max-width: 300px;">
            <div class="bg-white rounded shadow p-3">
                <h6 class="text-primary fw-bold mb-2">La contraseña debe cumplir con:</h6>
                <ul class="mb-0 small text-muted">
                    <li>Tener mínimo ocho caracteres.</li>
                    <li>Contener al menos un número.</li>
                    <li>Contener al menos una mayúscula y una minúscula.</li>
                </ul>
            </div>
        </div>
        
    </div>
@endsection

@section('endbody-scripts')
<script>
    const togglePasswordNew = document.getElementById('eyePasswordNew');
    const passwordNew = document.getElementById('password');
    togglePasswordNew.addEventListener('click', function (e) {
        const type = passwordNew.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordNew.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    const togglePasswordConfirm = document.getElementById('eyePasswordConfirm');
    const passwordConfirm = document.getElementById('confirmPassword');
    togglePasswordConfirm.addEventListener('click', function (e) {
        const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirm.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
</script>
@endsection
