<script id="template-show-conductor" type="text/x-handlebars-template">
@verbatim
	<div class="bg-light border rounded p-3">
		<h5>Información del Conductor</h5>
		<div class="row">
			<div class="form-group col-md-4">
				<label>RUT</label>
				<input type="text" class="form-control" value="{{rut}}" disabled>
			</div>
			<div class="form-group col-md-4">
				<label>Nombre</label>
				<input type="text" class="form-control" value="{{nombre}}" disabled>
			</div>
			<div class="form-group col-md-4">
				<label>Apellido</label>
				<input type="text" class="form-control" value="{{apellido}}" disabled>
			</div>
		</div>

		<div class="row mt-3">
			<div class="form-group col-md-4">
				<label>Región Operativa</label>
				<input type="text" class="form-control" value="{{region_operativa.nombre}}" disabled>
			</div>
			<div class="form-group col-md-4">
				<label>Empresa</label>
				<input type="text" class="form-control" value="{{empresa.razon_social}}" disabled>
			</div>
			<div class="form-group col-md-4">
				<label>Teléfono</label>
				<input type="text" class="form-control" value="{{telefono}}" disabled>
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
