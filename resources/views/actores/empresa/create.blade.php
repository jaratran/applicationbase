@extends('layouts.app')

@section('head-scripts')
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/personalizaciones-select2.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white fs-5">
                    Crear Empresa
                </div>
                <form action="{{ route('empresa.store') }}" method="POST" id="formCreate">
                    @csrf
                    <div class="card-body">
                        @include('includes.alertas-sistema')

                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Información General</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="rut_empresa">RUT</label>
                                    <input type="text" class="form-control" name="rut_empresa" id="rut_empresa" value="{{ old('rut_empresa') }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="razon_social">Razón Social</label>
                                    <input type="text" class="form-control" name="razon_social" id="razon_social" value="{{ old('razon_social') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Contacto y Ubicación</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono" id="telefono" value="{{ old('telefono') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="email_contacto">Correo de Contacto</label>
                                    <input type="email" class="form-control" name="email_contacto" id="email_contacto" value="{{ old('email_contacto') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="telefono_contacto">Teléfono de Contacto</label>
                                    <input type="text" class="form-control" name="telefono_contacto" id="telefono_contacto" value="{{ old('telefono_contacto') }}">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="form-group col-md-4">
                                    <label>Región</label>
                                    <select class="form-control select2" id="region_empresa" name="region_empresa" required>
                                        <option value="">Cargando Regiones ...</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Comuna</label>
                                    <select class="form-control select2" id="comuna_empresa" name="comuna_id" required></select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" class="form-control" name="direccion" id="direccion" value="{{ old('direccion') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Tipo de Empresa</h5>
                            <div class="row">
                                <!-- Select Tipo de Empresa -->
                                <div class="form-group col-md-4">
                                    <label for="tipo_empresa_id">Tipo de Empresa</label>
                                    <select class="form-control select2" id="tipo_empresa_id" name="tipo_empresa_id" required>
                                        <option value="">Cargando Tipos ...</option>
                                    </select>
                                </div>
                            </div> 
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary my-2"><i class="fa fa-edit"></i> Crear Empresa</button>
                        <button type="button" class="btn btn-secondary my-2" onclick="window.location.href='{{ route('empresa.index') }}'">
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
    <script src="{{ asset('js/select2.full.js') }}"></script>
    @include('includes.constantes-js-catalogo')

    <script>
        window.onload = function() {
            $(".select2").select2({theme: "bootstrap"});
        }
    </script>

    <script>
        $(document).ready(function () {
            function cargarComunas(regionID) {
                $.get("/parametros/comuna", {idRegion: regionID}, function(data) {
                    let html = '<option value="">Seleccione Comuna</option>';
                    data.forEach(c => {
                        html += `<option value="${c.id}">${c.nombre}</option>`;
                    });
                    $("#comuna_empresa").html(html);
                });
            }

            function cargarRegiones() {
                $.get("/parametros/region", {}, function(data) {
                    let html = '<option value="">Seleccione Región</option>';
                    data.forEach(r => {
                        html += `<option value="${r.id}">${r.nombre}</option>`;
                    });
                    $("#region_empresa").html(html);

                    $("#region_empresa").on("change", function () {
                        cargarComunas($(this).val());
                    });
                });
            }

            cargarRegiones();
         catalogo2select2(CATEGORIA_TIPO_EMPRESA, "tipo_empresa_id", "Seleccione Tipo de Empresa");
        });
    </script>

    <!-- Captura del Submit para colocar Spinner en el Botón y evitar doble Submit -->
    <script>
		$('#formCreate').on('submit', function (e) {
			// 🔁 Controlar doble submit
			// Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
			if (this.enviado) {
				console.log("⛔ Doble submit detectado, se detiene.");
				e.preventDefault();
				return;
			}

			const $btnSubmit = $('#formCreate button[type="submit"]');
			if ($btnSubmit.length) {
				// Deshabilita el botón para evitar segundo click
			    $btnSubmit.prop('disabled', true);
			    $btnSubmit.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
			}

			// 🧷 Flag para evitar siguiente submit
			this.enviado = true;
		});
    </script>
@endsection
