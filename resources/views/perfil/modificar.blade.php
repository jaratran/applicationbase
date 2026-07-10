@extends('layouts.app')

@section('head-scripts')
    <!-- Select2 CSS (mejora visual y funcional de elementos <select>) -->
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white fs-5">
                    Modificar Perfil
                </div>
                <div class="card-body">
                    @include('includes.alertas-sistema')

                    <form action="{{ route('perfil.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}

                        <h5>Datos Personales</h5>
                        <div class="row mb-3">
                            <div class="form-group col-md-4">
                                <label for="rut_usuario">Rut</label>
                                <input type="text" class="form-control rut" name="rut_usuario" id="rut_usuario"
                                    value="{{ $user->rut_usuario }}" placeholder="Ej: 12.345.678-9" required maxlength="12" autofocus>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="nombre_usuario">Nombres</label>
                                <input type="text" class="form-control" name="nombre_usuario" id="nombre_usuario"
                                    value="{{ $user->nombre_usuario }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="apellidos_usuario">Apellidos</label>
                                <input type="text" class="form-control" name="apellidos_usuario" id="apellidos_usuario"
                                    value="{{ $user->apellidos_usuario }}" required>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="form-group col-md-4">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" class="form-control" name="email" id="email"
                                    value="{{ $user->email }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telefono">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" id="telefono"
                                    value="{{ $user->telefono }}" required placeholder="+ 569 11 111 111">
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="form-group col-md-4">
                                <label for="rol_id">Rol de Usuario</label>
                                <input type="text" class="form-control" value="{{ $user->rol->nombre }}" disabled>
                                <input type="hidden" name="rol_id" value="{{ old('rol_id', $user->rol_id) }}">
                            </div>

                            @switch($user->rol_id)
                                @case(config('constantes.ROL_SOLICITANTE_PLANTA'))
                                    <div class="form-group col-md-4">
                                        <label for="sucursal_usuario">Sucursal</label>
                                        <input type="text" class="form-control" id="sucursal_usuario" name="sucursal_usuario" value="{{ $user->sucursal->nombre_sucursal }}" disabled>
                                    </div>
                                    @break

                                @case(config('constantes.ROL_SOLICITANTE_PRODUCTOR'))
                                    <div class="form-group col-md-4">
                                        <label for="empresa_usuario">Empresa</label>
                                        <input type="text" class="form-control" id="empresa_usuario" name="empresa_usuario" value="{{ $user->empresa->razon_social }}" disabled>
                                    </div>
                                    @break
                            @endswitch
                        </div>

                        <h5>Dirección</h5>
                        <div class="direccion row my-3">
                            <div class="form-group col-md-4">
                                <label>Región</label>
                                <select class="form-control select2" id="region_usuario" name="region_usuario" required>
                                    <option value="">{{ $user->comuna->region->nombre }}</option>
                                </select>
                                <input type="hidden" id="region_actual" value="{{ $user->comuna->region_id }}">
                            </div>
                        
                            <div class="form-group col-md-4">
                                <label>Comuna</label>
                                <select class="form-control select2" id="comuna_usuario" name="comuna_id" required>
                                    <option value="">{{ $user->comuna->nombre }}</option>
                                </select>
                                <input type="hidden" id="comuna_seleccionada" value="{{ $user->comuna_id }}">
                            </div>
                        
                            <div class="form-group col-md-4">
                                <label>Dirección</label>
                                <input class="form-control" name="direccion" value="{{ $user->direccion }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary my-2"><i class="fa fa-edit"></i> Actualizar Perfil</button>
                        <button type="button" class="btn btn-secondary my-2" onclick="window.location.href='{{ url('/perfil') }}'">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    <!-- Select2 JavaScript (extensión de elementos <select> con búsqueda, multiselección, etc.) -->
    <script src="{{ asset('js/select2.full.js') }}"></script>
    @include('includes.constantes-js-catalogo')
    <script>
        window.onload = function() {
            $(".select2").select2({theme: "bootstrap"});
        }
    </script>
    
    <script>
        $(document).ready(function () {
            function direccionComuna(idRegion) {
                $.get("/parametros/comuna",{idRegion:idRegion},function(data) {
                    var getComuna = $("#comuna_seleccionada").val();
                    var comunaUsuario = '<option value="">Seleccione Comuna</option>';

                    for (var i = 0;i<data.length;i++) {
                        comunaUsuario+='<option value="'+data[i]['id']+'"';
                        if (getComuna==data[i]['id']) {
                            comunaUsuario+=" selected";
                        }
                        comunaUsuario+='>'+data[i]['nombre']+'</option>';
                    }

                    $("#comuna_usuario").html(comunaUsuario);
                });
            }
            function direccionRegion() {
                $.get("/parametros/region",{},function(data) {
                    var getRegion = $("#region_actual").val();
                    var regionUsuario = '<option value="">Seleccione Región</option>';

                    for (var i = 0;i<data.length;i++) {
                        regionUsuario+='<option value="'+data[i]['id']+'"';
                        if (getRegion==data[i]['id']) {
                            regionUsuario+=" selected";
                        }
                        regionUsuario+='>'+data[i]['nombre']+'</option>';
                    }
                    $("#region_usuario").html(regionUsuario);

                    // Cargar comunas al inicio (una vez seleccionada la región actual)
                    if (getRegion && getRegion !== "") {
                        direccionComuna(getRegion);
                    }

                    // Cargar comunas al cambiar de región	
                    $("#region_usuario").on("change", function () {
                        let nuevaRegion = $(this).val();
                        direccionComuna(nuevaRegion);
                    });                
                });
            }

            direccionRegion();
            sucursalUsuario();
        });
    </script>
@endsection
