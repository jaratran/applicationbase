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
					Nueva Rampla
				</div>

				<form action="{{ route('rampla.store') }}" method="POST" id="formCreate">
					@csrf

					<div class="card-body">
						@include('includes.alertas-sistema')

						<div class="bg-light border rounded p-3 mb-4">
							<h5>Información de la Rampla</h5>

							<div class="row">
								<div class="form-group col-md-4">
									<label for="patente">Patente</label>
									<input type="text"
										class="form-control"
										id="patente"
										name="patente"
										value="{{ old('patente') }}"
										required>
								</div>

								<!-- <div class="form-group col-md-4">
									<label for="region_operativa_id">Región Operativa</label>
									<select class="form-control select2"
											id="region_operativa_id"
											name="region_operativa_id"
											required>
										<option value="">Seleccione Región Operativa</option>
									</select>
								</div> -->

								<div class="form-group col-md-4">
									<label>Región Operativa</label>

									<!-- Visible, solo informativo -->
									<input type="text"
										class="form-control"
										value="XII"
										disabled>

									<!-- Valor real que viaja al backend -->
									<input type="hidden"
										name="region_operativa_id"
										value="{{ config('constantes.REGION_XII') }}">
								</div>

								<div class="form-group col-md-4">
									<label for="tipo_rampla_id">Tipo de Rampla</label>
									<select class="form-control select2"
											id="tipo_rampla_id"
											name="tipo_rampla_id"
											required>
										<option value="">Seleccione Tipo de Rampla</option>
									</select>
								</div>
							</div>

							<div class="row mt-3">
								<div class="form-group col-md-4">
									<label for="capacidad_rampla_id">Capacidad</label>
									<select class="form-control select2"
											id="capacidad_rampla_id"
											name="capacidad_rampla_id"
											required>
										<option value="">Seleccione Capacidad</option>
									</select>
								</div>

								<div class="form-group col-md-4">
									<label for="estado_rampla_id">Estado de Rampla</label>
									<select class="form-control select2"
											id="estado_rampla_id"
											name="estado_rampla_id"
											required>
										<option value="">Seleccione Estado</option>
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="card-footer">
						<button type="submit" class="btn btn-primary my-2">
							<i class="fa fa-save"></i> Guardar Rampla
						</button>

						<button type="button"
								class="btn btn-secondary my-2"
								onclick="window.location.href='{{ route('rampla.index') }}'">
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
		window.onload = function () {
			$(".select2").select2({ theme: "bootstrap" });
		};
	</script>

	<script>
		$(document).ready(function () {
			// Regiones operativas
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

			cargarRegionesOperativas();

			// Catálogos Rampla
			catalogo2select2( CATEGORIA_TIPO_RAMPLA, "tipo_rampla_id", "Seleccione Tipo de Rampla" );
			catalogo2select2( CATEGORIA_CAPACIDAD_RAMPLA, "capacidad_rampla_id", "Seleccione Capacidad" );
			catalogo2select2( CATEGORIA_ESTADO_RAMPLA, "estado_rampla_id", "Seleccione Estado" );
		});
	</script>

	{{-- Control doble submit --}}
	<script>
		$('#formCreate').on('submit', function (e) {
			if (this.enviado) {
				e.preventDefault();
				return;
			}

			const $btn = $('#formCreate button[type="submit"]');
			$btn.prop('disabled', true);
			$btn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

			this.enviado = true;
		});
	</script>
@endsection
