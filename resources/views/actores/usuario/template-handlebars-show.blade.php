<script id="template-show-trabajador" type="text/x-handlebars-template">
@verbatim
    <div class="bg-light border rounded p-3 mb-4">
        <h5>Datos Personales</h5>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Rut</label>
                <input type="text" class="form-control" value="{{rut_usuario}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Nombres</label>
                <input type="text" class="form-control" value="{{nombre_usuario}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Apellidos</label>
                <input type="text" class="form-control" value="{{apellidos_usuario}}" disabled>
            </div>
        </div>

        <div class="row align-items-stretch">
            <div class="col-md-8">
				<div class="row">
					<div class="form-group col-md-6 mt-4">
						<label>Correo Electrónico</label>
						<input type="text" class="form-control" value="{{email}}" disabled>
					</div>
					<div class="form-group col-md-6 mt-4">
						<label>Teléfono</label>
						<input type="text" class="form-control" value="{{telefono}}" disabled>
					</div>
                </div>

				<div class="row">
                    <div class="form-group col-md-6 mt-3">
                        <label>Rol Usuario</label>
                        <input type="text" class="form-control" value="{{rol.nombre}}" disabled>
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        {{#if (eq rol.id ROL_SOLICITANTE_PLANTA)}}
                            <label>Planta de Proceso</label>
                            <input type="text" class="form-control" value="{{sucursal.nombre_sucursal}}" disabled>
                        {{else if (eq rol.id ROL_SOLICITANTE_PRODUCTOR)}}
                            <label>Productor de Materia Prima</label>
                            <input type="text" class="form-control" value="{{empresa.razon_social}}" disabled>
                        {{/if}}
                    </div>
                </div>

				<div class="row">
                    <div class="form-group col-md-6 mt-3">
						<label>Región Operativa</label>
						<input type="text" class="form-control" value="{{region_operativa_codigo}}" disabled>
					</div>
                </div>
            </div>

            <div class="col-md-4 d-flex flex-column align-items-center justify-content-center mt-3">
                <label>Avatar</label>
                <img src="{{avatar_url}}"
                     alt="Avatar Mediano"
                     class="img-thumbnail mt-2"
                     style="width: 180px; height: auto;">
            </div>
        </div>
    </div>

    <div class="bg-light border rounded p-3">
        <h5>Dirección</h5>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Dirección</label>
                <input type="text" class="form-control" value="{{direccion}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Comuna</label>
                <input type="text" class="form-control" value="{{comuna.nombre}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Región</label>
                <input type="text" class="form-control" value="{{comuna.region.nombre}}" disabled>
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
