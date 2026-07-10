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
                    Editar Sucursal
                </div>
                <form action="{{ route('sucursal.update', $sucursal->id) }}" method="POST" id="formEdit">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @include('includes.alertas-sistema')

                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Información General</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="nombre_sucursal">Nombre</label>
                                    <input type="text" class="form-control" name="nombre_sucursal" id="nombre_sucursal" value="{{ $sucursal->nombre_sucursal }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="codigo_siep">Código Siep</label>
                                    <input type="number" class="form-control" name="codigo_siep" id="codigo_siep" value="{{ $sucursal->codigo_siep }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="tipo_sucursal_id">Tipo de Sucursal</label>
                                    <select class="form-control select2" id="tipo_sucursal_id" name="tipo_sucursal_id" required>
                                        <option value="">{{ $sucursal->tipoSucursal->Nombre ?? '-' }}</option>
                                    </select>
                                    <input type="hidden" id="tipo_actual" value="{{ $sucursal->tipo_sucursal_id }}">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="form-group col-md-4">
                                    <label for="email">Correo Electrónico</label>
                                    <input type="email" class="form-control" name="email" id="email" value="{{ $sucursal->email }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono" id="telefono" value="{{ $sucursal->telefono }}">
                                </div>
                            </div>
                        </div>

                        <div class="bg-light border rounded p-3">
                            <h5>Ubicación y Detalles</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Región</label>
                                    <select class="form-control select2" id="region_sucursal" required>
                                        <option value="">{{ $sucursal->comuna->region->nombre ?? '-' }}</option>
                                    </select>
                                    <input type="hidden" id="region_actual" value="{{ $sucursal->comuna->region_id }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Comuna</label>
                                    <select class="form-control select2" id="comuna_sucursal" name="comuna_id" required>
                                        <option value="">{{ $sucursal->comuna->nombre ?? '-' }}</option>
                                    </select>
                                    <input type="hidden" id="comuna_seleccionada" value="{{ $sucursal->comuna_id }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="zona_id">Zona</label>
                                    <select class="form-control select2" id="zona_id" name="zona_id" required>
                                        <option value="">{{ $sucursal->zona->Nombre ?? '-' }}</option>
                                    </select>
                                    <input type="hidden" id="zona_actual" value="{{ $sucursal->zona_id }}">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="form-group col-md-4">
                                    <label for="km">Kilómetros</label>
                                    <input type="number" class="form-control" name="km" id="km" value="{{ $sucursal->km }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="tiempo_estimado_viaje">Tiempo Estimado</label>
                                    <input type="number" step="0.01" class="form-control" name="tiempo_estimado_viaje" id="tiempo_estimado_viaje" value="{{ $sucursal->tiempo_estimado_viaje }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary my-2"><i class="fa fa-edit"></i> Actualizar Sucursal</button>
                        <button type="button" class="btn btn-secondary my-2" onclick="window.location.href='{{ route('sucursal.index') }}'">
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
                $.get("/parametros/comuna",{idRegion:regionID},function(data) {
                    var actual = $("#comuna_seleccionada").val();
                    var html = '<option value="">Seleccione Comuna</option>';
                    data.forEach(c => {
                        html += `<option value="${c.id}"${c.id == actual ? ' selected' : ''}>${c.nombre}</option>`;
                    });
                    $("#comuna_sucursal").html(html);
                });
            }

            function cargarRegionesOperativas() {
                $.get("/parametros/region-operativa",{},function(data) {
                    var actual = $("#region_actual").val();
                    var html = '<option value="">Seleccione Región</option>';
                    data.forEach(r => {
                        html += `<option value="${r.id}"${r.id == actual ? ' selected' : ''}>${r.nombre}</option>`;
                    });
                    $("#region_sucursal").html(html);

                    if (actual && actual !== "") {
                        cargarComunas(actual);
                    }

                    $("#region_sucursal").on("change", function () {
                        cargarComunas($(this).val());
                    });
                });
            }

            cargarRegionesOperativas();

			catalogo2select2(CATEGORIA_ZONA_SUCURSAL, "zona_id",         "Seleccione Zona de Sucursal", "zona_actual");		// Edit si considera valor pre-cargado
        	catalogo2select2(CATEGORIA_TIPO_SUCURSAL, "tipo_sucursal_id","Seleccione Tipo de Sucursal", "tipo_actual");		// Edit si considera valor pre-cargado
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
