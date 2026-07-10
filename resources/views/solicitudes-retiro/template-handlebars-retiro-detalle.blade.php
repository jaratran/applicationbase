<script id="template-retiro-detalle" type="text/x-handlebars-template">
    @verbatim
        <div class="bg-light border rounded p-3 mb-4">
            <h5>Datos del Solicitante</h5>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Nombre Usuario</label>
                    <input type="text" class="form-control" value="{{solicitud.usuario.nombre_usuario}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Apellidos Usuario</label>
                    <input type="text" class="form-control" value="{{solicitud.usuario.apellidos_usuario}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Rol de Usuario</label>
                    <input type="text" class="form-control" value="{{solicitud.usuario.rol.nombre}}" disabled>
                </div>
            </div>
        </div>

        <div class="bg-light border rounded p-3 mb-4">
            <h5>Datos del Retiro Solicitado</h5>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>N° de Retiro</label>
                    <input type="text" class="form-control" value="{{id}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Fecha de la Solicitud</label>
                    <input type="text" class="form-control" value="{{solicitud.created_at_format}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Estado del Retiro</label>
                    <input type="text" class="form-control" value="{{estado.nombre}}" disabled>
                </div>
            </div>
            <div class="row mt-3">
                <div class="form-group col-md-4">
                    <label>Proveedor</label>
                    <input type="text" class="form-control" value="{{solicitud.maquila.empresa.razon_social}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Planta</label>
                    <input type="text" class="form-control" value="{{solicitud.maquila.sucursal.nombre_sucursal}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Región</label>
                    <input type="text" class="form-control" value="{{solicitud.region_operativa_codigo}}" disabled>
                </div>
            </div>
        </div>

        <div class="bg-light border rounded p-3">
            <h5>Detalle del Retiro</h5>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Fecha y horario de Retiro</label>
                    <input type="text" class="form-control" value="{{fecha_retiro_format}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Tipo de Retiro</label>
                    <input type="text" class="form-control" value="{{tipo_retiro.nombre}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Kilogramos estimados</label>
                    <input type="text" class="form-control" value="{{kilogramos_estimados}}" disabled>
                </div>
            </div>

            {{#if esBin}}
                <div class="row my-3">
                    <div class="form-group col-md-4">
                        <label>Requiere reposición de Bins</label>
                        {{#if requiere_reposicion}}
                            <input type="text" class="form-control" value="Sí" disabled>
                        {{else}}
                            <input type="text" class="form-control" value="No" disabled>
                        {{/if}}
                    </div>

                    {{#if cantidad_bins}}
                        <div class="form-group col-md-4">
                            <label>Cantidad de Bins a reponer</label>
                            <input type="text" class="form-control" value="{{cantidad_bins}}" disabled>
                        </div>
					{{else}}
                        <div class="form-group col-md-4">
                        </div>
                    {{/if}}
					<div class="form-group col-md-4">
						<label>Tipo de Operación</label>
						{{#if esRetiro}}
							<input type="text" class="form-control" value="Retiro" disabled>
						{{else}}
							<input type="text" class="form-control" value="Reposición" disabled>
						{{/if}}
					</div>
                </div>
			{{else}}
                <div class="row my-3">
					<div class="form-group col-md-4">
					</div>
					<div class="form-group col-md-4">
					</div>
					<div class="form-group col-md-4">
						<label>Tipo de Operación</label>
						{{#if esRetiro}}
							<input type="text" class="form-control" value="Retiro" disabled>
						{{else}}
							<input type="text" class="form-control" value="Reposición" disabled>
						{{/if}}
					</div>
                </div>
            {{/if}}
        </div>

        <!-- Historial de Comentarios al Retiro -->
        {{#if comentarios.length}}
            <div class="row my-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Historial de Comentarios</h5>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#comentariosCollapse"
                            aria-expanded="false" aria-controls="comentariosCollapse" id="toggleComentariosBtn">
                            <i class="fa fa-chevron-down me-1" id="comentariosIconToggle"></i>
                            <span id="comentariosTextToggle">Mostrar</span>
                    </button>
                </div>

                <div class="collapse" id="comentariosCollapse">
                    <div class="border rounded p-3" style="max-height: 280px; overflow-y: auto;">
                        {{#each comentarios}}
                            <div class="comentario mb-2">
                                <small class="text-muted">[{{created_at_format}}] {{usuario.nombre_usuario}} {{usuario.apellidos_usuario}}:</small>
                                <p class="mb-0">{{comentario}}</p>
                            </div>
                        {{/each}}
                    </div>
                </div>
            </div>
        {{/if}}

        <!-- Historial de Cambios del Retiro -->
        {{#if historial.length}}
            <div class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Historial de Cambios del Retiro</h5>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#cambiosCollapse"
                            aria-expanded="true" aria-controls="cambiosCollapse" id="toggleCambiosBtn">

                            <i class="fa fa-chevron-down me-1" id="cambiosIconToggle"></i>
                            <span id="cambiosTextToggle">Mostrar</span>
                    </button>
                </div>

                <div class="collapse" id="cambiosCollapse">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Fecha y Hora del retiro</th>
                                    <th>Tipo de retiro</th>
                                    <th>Kilogramos estimados</th>
                                    <th>¿Requiere reposición de Bins?</th>
                                    <th>Cantidad de Bins a reponer</th>
                                    <th>Fecha y Hora del cambio</th>
                                    <th>Usuario que realizó el cambio</th>
                                    <!--<th>Motivo del cambio</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                {{#each historial}}
                                    <tr>
                                        <td>{{fecha_retiro_format}}</td>
                                        <td>{{tipo_retiro.nombre}}</td>
                                        <td>{{kilogramos_estimados}}</td>
                                        <td>{{#if requiere_reposicion}}Sí{{else}}No{{/if}}</td>
                                        <td>{{cantidad_bins}}</td>
                                        <td>{{created_at_format}}</td>
                                        <td>{{usuario.nombre_usuario}} {{usuario.apellidos_usuario}}</td>
                                        <!--<td>{{motivo_cambio}}</td>-->
                                    </tr>
                                {{/each}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        {{/if}}

        {{#if esCancelado}}
            <div class="bg-light border rounded p-3 mt-4">
                <h5>Comentarios</h5>
                <div class="form-group col-md-12">
                    <label>Coomentario de Anulación</label>
                    <textarea class="form-control" rows="2" disabled>{{comentario_anulacion}}</textarea>
                </div>
            </div>
        {{/if}}
    @endverbatim
</script>
