@extends('layouts.app')

@section('head-scripts')
    <!-- Select2 CSS (mejora visual y funcional de elementos <select>) -->
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap.css') }}">
    <!-- Personalizar select2 en cuanto a altura de selector y color de opción destacada -->
    <link rel="stylesheet" href="{{ asset('css/personalizaciones-select2.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white fs-5">
                    Editar Empresa
                </div>
                <form action="{{ route('empresa.update', $empresa->id) }}" method="POST" id="formEdit">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @include('includes.alertas-sistema')

                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Información General</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="rut_empresa">RUT</label>
                                    <input type="text" class="form-control" name="rut_empresa" id="rut_empresa" value="{{ $empresa->rut_empresa }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="razon_social">Razón Social</label>
                                    <input type="text" class="form-control" name="razon_social" id="razon_social" value="{{ $empresa->razon_social }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Contacto y Ubicación</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono" id="telefono" value="{{ $empresa->telefono }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="email_contacto">Correo de Contacto</label>
                                    <input type="email" class="form-control" name="email_contacto" id="email_contacto" value="{{ $empresa->email_contacto }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="telefono_contacto">Teléfono de Contacto</label>
                                    <input type="text" class="form-control" name="telefono_contacto" id="telefono_contacto" value="{{ $empresa->telefono_contacto }}">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="form-group col-md-4">
                                    <label>Región</label>
                                    <select class="form-control select2" id="region_empresa" required>
                                        <option value="">{{ $empresa->comuna->region->nombre ?? '-' }}</option>
                                    </select>
                                    <input type="hidden" id="region_actual" value="{{ $empresa->comuna->region_id }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Comuna</label>
                                    <select class="form-control select2" id="comuna_empresa" name="comuna_id" required>
                                        <option value="">{{ $empresa->comuna->nombre ?? '-' }}</option>
                                    </select>
                                    <input type="hidden" id="comuna_seleccionada" value="{{ $empresa->comuna_id }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" class="form-control" name="direccion" id="direccion" value="{{ $empresa->direccion }}" required>
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
                                        <option value="">{{ $empresa->tipoEmpresa->Nombre ?? '-' }}</option>
                                    </select>
                                    <input type="hidden" id="tipo_actual" value="{{ $empresa->tipo_empresa_id }}">
                                </div>
                            </div> 
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary my-2"><i class="fa fa-edit"></i> Actualizar Empresa</button>
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
                $.get("/parametros/comuna", {idRegion: regionID}, function (data) {
                    const actual = $("#comuna_seleccionada").val();
                    let html = '<option value="">Seleccione Comuna</option>';
                    data.forEach(c => {
                        html += `<option value="${c.id}"${c.id == actual ? ' selected' : ''}>${c.nombre}</option>`;
                    });
                    $("#comuna_empresa").html(html);
                });
            }
            function cargarRegiones() {
                $.get("/parametros/region", {}, function (data) {
                    const actual = $("#region_actual").val();
                    let html = '<option value="">Seleccione Región</option>';
                    data.forEach(r => {
                        html += `<option value="${r.id}"${r.id == actual ? ' selected' : ''}>${r.nombre}</option>`;
                    });
                    $("#region_empresa").html(html);

                    if (actual && actual !== "") {
                        cargarComunas(actual);
                    }

                    $("#region_empresa").on("change", function () {
                        cargarComunas($(this).val());
                    });
                });
            }

        	catalogo2select2(CATEGORIA_TIPO_EMPRESA, "tipo_empresa_id", "Seleccione Tipo de Empresa", "tipo_actual");		// Edit si considera valor pre-cargado
            cargarRegiones();
        });
    </script>

    <!-- Captura del Submit para colocar Spinner en el Botón y evitar doble Submit -->
    <script>
		$('#formEdit').on('submit', function (e) {
			// 🔁 Controlar doble submit
			// Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
			if (this.enviado) {
				console.log("⛔ Doble submit detectado, se detiene.");
				e.preventDefault();
				return;
			}

			const $btnSubmit = $('#formEdit button[type="submit"]');
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
