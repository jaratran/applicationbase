<script id="template-show-camion" type="text/x-handlebars-template">
@verbatim
    <div class="bg-light border rounded p-3 mb-4">
        <h5>Información del Camión</h5>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Patente</label>
                <input type="text" class="form-control" value="{{patente}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Empresa</label>
                <input type="text" class="form-control" value="{{empresa.razon_social}}" disabled>
            </div>
			<div class="form-group col-md-4">
				<label>Región Operativa</label>
				<input type="text" class="form-control" value="{{region_operativa.nombre}}" disabled>
			</div>
        </div>

        <div class="row mt-3">
			<div class="form-group col-md-4">
                <label>Tipo de Camión</label>
                <input type="text" class="form-control" value="{{tipo_camion.nombre}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Rendimiento Óptimo</label>
                <input type="text" class="form-control" value="{{rendimiento_optimo}}" disabled>
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group col-md-4">
                <label>Arrendado</label>
                <input type="text" class="form-control" value="{{#if arrendado}}Sí{{else}}No{{/if}}" disabled>
            </div>
        </div>
	</div>

    <div class="bg-light border rounded p-3">
        <h5>Información del Conductor</h5>
        <div class="form-group col-md-4">
            <label>Conductor por Defecto</label>
            <input type="text" class="form-control" value="{{conductor.nombre}} {{conductor.apellido}}" disabled>
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
