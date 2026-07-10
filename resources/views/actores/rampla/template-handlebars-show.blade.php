<script id="template-show-rampla" type="text/x-handlebars-template">
@verbatim
	<div class="bg-light border rounded p-3 mb-4">
		<h5>Información de la Rampla</h5>

		<div class="row">
			<div class="form-group col-md-4">
				<label>Patente</label>
				<input type="text" class="form-control" value="{{patente}}" disabled>
			</div>

			<div class="form-group col-md-4">
				<label>Región Operativa</label>
				<input type="text" class="form-control" value="{{region_operativa_codigo}}" disabled>
			</div>

			<div class="form-group col-md-4">
				<label>Tipo de Rampla</label>
				<input type="text" class="form-control" value="{{tipo_rampla.nombre}}" disabled>
			</div>
		</div>

		<div class="row mt-3">
			<div class="form-group col-md-4">
				<label>Capacidad</label>
				<input type="text" class="form-control" value="{{capacidad_rampla.nombre}}" disabled>
			</div>

			<div class="form-group col-md-4">
				<label>Estado de Rampla</label>
				<input type="text" class="form-control" value="{{estado_rampla.nombre}}" disabled>
			</div>
		</div>
	</div>

	{{#unless activo}}
		<div class="bg-light border rounded p-3 mt-4">
			<h5>Observaciones</h5>
			<div class="form-group col-md-12">
				<label>Observación de Inactividad</label>
				<textarea class="form-control" rows="2" disabled>{{observacion_inactividad}}</textarea>
			</div>
		</div>
	{{/unless}}
@endverbatim
</script>
