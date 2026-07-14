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
                    Crear Camión
                </div>
                <form action="{{ route('camion.store') }}" method="POST" id="formCreate">
                    @csrf
                    <div class="card-body">
                        @include('includes.alertas-sistema')

                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Información del Camión</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="patente">Patente</label>
                                    <input type="text" class="form-control" name="patente" id="patente" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="empresa_id">Empresa</label>
                                    <select class="form-control select2" id="empresa_id" name="empresa_id" required></select>
                                </div>
                            </div>

                            <div class="row mt-3">
								<div class="form-group col-md-4">
									<label for="region_operativa_id">Región Operativa</label>
									<select class="form-control select2" id="region_operativa_id" name="region_operativa_id" required>
										<option value="">Cargando Regiones ...</option>
									</select>
								</div>
                                <div class="form-group col-md-4">
                                    <label for="tipo_camion_id">Tipo de Camión</label>
                                    <select class="form-control select2" id="tipo_camion_id" name="tipo_camion_id" required></select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="rendimiento_optimo">Rendimiento Óptimo</label>
                                    <input type="number" step="0.01" class="form-control" name="rendimiento_optimo" id="rendimiento_optimo">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="form-group col-md-4">
                                    <label for="arrendado">¿Está Arrendado?</label>
                                    <select class="form-control" name="arrendado" id="arrendado">
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                </div>
                            </div>
						</div>

                        <div class="bg-light border rounded p-3">
                            <h5>Información del Conductor</h5>
                            <div class="row mt-3">
                                <div class="form-group col-md-4">
                                    <label for="conductor_id">Conductor por Defecto</label>
                                    <select class="form-control select2" id="conductor_id" name="conductor_id" required></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary my-2"><i class="fa fa-plus"></i> Crear Camión</button>
                        <button type="button" class="btn btn-secondary my-2" onclick="window.location.href='{{ route('camion.index') }}'">
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
            $(".select2").select2({ theme: "bootstrap" });
        }
    </script>

    <script>
        $(document).ready(function () {
            function cargarConductorDefecto(empresaID) {
                $.get("/conductores/empresa/" + empresaID, function(data) {
                    let html = '<option value="">Seleccione Conductor</option>';
                    data.forEach(s => {
                        html += `<option value="${s.id}">${s.nombre} ${s.apellido}</option>`;
                    });
                    $("#conductor_id").html(html);
                });
            }
            function transportistaCamion() {
                $.get("/empresas/tipo/" + TIPO_EMPRESA_TRANSPORTISTA ,function(data) {  // tipo_empresa = 33 -> Id de Transportista en Catalogos
                    var getSuc = $("#empresa_actual").val();
                    var transportistaCamion = '<option value="">Seleccione Transportista de Camión</option>';
                    for (var i = 0;i<data.length;i++) {
                        transportistaCamion+='<option value="'+data[i]['id']+'"';
                        if (getSuc==data[i]['id']) {
                            transportistaCamion+=" selected";
                        }
                        transportistaCamion+='>'+data[i]['razon_social']+'</option>';
                    }
                    $("#empresa_id").html(transportistaCamion);

                    $("#empresa_id").on("change", function () {
                        cargarConductorDefecto($(this).val());
                    });
                });
            }
			function cargarRegionesOperativas() {
				$.get("/parametros/region-operativa", function(data) { // Regiones operativas vía SQL a la base : 10 y 12
					var getReg = $("#region_operativa_actual").val();
					var regionCamion = '<option value="">Seleccione Región Operativa de Camion</option>';
					for (var i = 0; i < data.length; i++) {
						regionCamion+='<option value="'+data[i]['id']+'"';
						if (getReg==data[i]['id']) {
							regionCamion+=" selected";
						}
						regionCamion+='>'+data[i]['nombre']+'</option>';
					}
					$("#region_operativa_id").html(regionCamion);
				});
			}

            transportistaCamion();
			cargarRegionesOperativas();
			catalogo2select2(CATEGORIA_TIPO_CAMION, "tipo_camion_id", "Seleccione Tipo de Camion");				// Create no considera valor pre-cargado
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
