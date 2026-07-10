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
					Crear Conductor
				</div>
				<form action="{{ route('conductor.store') }}" method="POST" id="formCreate">
					@csrf
					<div class="card-body">
						@include('includes.alertas-sistema')

						<div class="bg-light border rounded p-3 mb-4">
							<h5>Información del Conductor</h5>
							<div class="row">
								<div class="form-group col-md-4">
									<label for="rut">RUT</label>
									<input type="text" class="form-control" name="rut" id="rut" required>
								</div>
								<div class="form-group col-md-4">
									<label for="nombre">Nombre</label>
									<input type="text" class="form-control" name="nombre" id="nombre" required>
								</div>
								<div class="form-group col-md-4">
									<label for="apellido">Apellido</label>
									<input type="text" class="form-control" name="apellido" id="apellido" required>
								</div>
							</div>

							<div class="row mt-3">
								<div class="form-group col-md-4">
									<label for="empresa_id">Empresa</label>
									<select class="form-control select2" id="empresa_id" name="empresa_id" required>
										<option value="">Cargando Empresas ...</option>
									</select>
								</div>

								<div class="form-group col-md-4">
									<label for="region_operativa_id">Región Operativa</label>
									<select class="form-control select2" id="region_operativa_id" name="region_operativa_id" required>
										<option value="">Cargando Regiones ...</option>
									</select>
								</div>

								<div class="form-group col-md-4">
									<label for="telefono">Teléfono</label>
									<input type="text" class="form-control" name="telefono" id="telefono" required>
								</div>
							</div>
						</div>
					</div>

					<div class="card-footer">
						<button type="submit" class="btn btn-primary my-2"><i class="fa fa-plus"></i> Crear Conductor</button>
						<button type="button" class="btn btn-secondary my-2" onclick="window.location.href='{{ route('conductor.index') }}'">
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
		$(".select2").select2({
			theme: "bootstrap"
		});
	}
</script>

<script>
	$(document).ready(function() {
		function transportistaConductor() {
			$.get("/empresas/tipo/" + TIPO_EMPRESA_TRANSPORTISTA, function(data) { // tipo_empresa = 33 -> Id de Transportista en Catalogos
				var getSuc = $("#empresa_actual").val();
				var transportistaConductor = '<option value="">Seleccione Transportista de Conductor</option>';
				for (var i = 0; i < data.length; i++) {
					transportistaConductor += '<option value="' + data[i]['id'] + '"';
					if (getSuc == data[i]['id']) {
						transportistaConductor += " selected";
					}
					transportistaConductor += '>' + data[i]['razon_social'] + '</option>';
				}
				$("#empresa_id").html(transportistaConductor);
			});
		}

		function cargarRegionesOperativas() {
			$.get("/parametros/region-operativa", function(data) { // Regiones operativas vía SQL a la base : 10 y 12
				var getReg = $("#region_operativa_actual").val();
				var regionConductor = '<option value="">Seleccione Región Operativa de Conductor</option>';
				for (var i = 0; i < data.length; i++) {
					regionConductor+='<option value="'+data[i]['id']+'"';
					if (getReg==data[i]['id']) {
						regionConductor+=" selected";
					}
					regionConductor+='>'+data[i]['nombre']+'</option>';
				}
				$("#region_operativa_id").html(regionConductor);
			});
		}

		transportistaConductor();
		cargarRegionesOperativas();
	});
</script>

<!-- Captura del Submit para colocar Spinner en el Botón y evitar doble Submit -->
<script>
	$('#formCreate').on('submit', function(e) {


		console.log("Llegue al submit ... ");


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
