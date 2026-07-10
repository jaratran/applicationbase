<script id="template-region-xii-show-planificacion" type="text/x-handlebars-template">
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
                    <label>Planta</label>
                    <input type="text" class="form-control" value="{{solicitud.maquila.sucursal.nombre_sucursal}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Proveedor</label>
                    <input type="text" class="form-control" value="{{solicitud.maquila.empresa.razon_social}}" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label>Región Operativa</label>
                    <input type="text" class="form-control" value="{{solicitud.region_operativa_codigo}}" disabled>
                </div>
            </div>
        </div>

        <div class="bg-light border rounded p-3 mb-4">
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

            <!-- Historial de Comentarios al Retiro -->
            {{#if comentarios.length}}
                <div class="row my-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Historial de Comentarios</h5>
                        <button class="btn btn-sm btn-outline-secondary d-flex align-items-center" type="button"
                        data-bs-toggle="collapse" data-bs-target="#comentariosCollapse"
                                aria-expanded="true" aria-controls="comentariosCollapse" id="toggleComentariosBtn">
                                <i class="fa fa-chevron-down me-1" id="iconToggleComentarios"></i>
                            <span id="textToggleComentarios">Mostrar</span>
                        </button>
                    </div>
                    <div class="collapse" id="comentariosCollapse">
                        <div class="border rounded p-3" style="max-height: 280px; overflow-y: auto;">
                            {{#each comentarios}}
                                <div class="comentario mb-2">
                                    <small class="text-muted">[{{created_at_format}}] {{usuario.nombre_usuario}}:</small>
                                    <p class="mb-0">{{comentario}}</p>
                                </div>
                            {{/each}}
                        </div>
                    </div>
                </div>
            {{/if}}
        </div>

		{{#if esRetiro}}

			{{#if noEstadoAceptado}}
				<div class="bg-light border rounded p-3">
					<h5>Datos de la Planificación del Retiro</h5>
					<div class="row">
						<div class="form-group col-md-4">
							<label>Fecha y horario planificado</label>
							<input type="datetime-local" class="form-control" value="{{planificacion.fecha_hora_planificada_format}}" disabled>
						</div>
						<div class="form-group col-md-4">
							<label>Horas de viaje a Planta La Portada</label>
							<input type="text" class="form-control" value="{{planificacion.duracion_dias}}" disabled>
						</div>
						<div class="form-group col-md-4">
							<label>Hora estimada de llegada a Planta la Portada</label>
							<input type="datetime-local" class="form-control" value="{{planificacion.fecha_hora_llegada_format}}" disabled>
						</div>
					</div>
					<div class="row my-3">
						<div class="form-group col-md-4">
							<label>Tipo de Materia Prima</label>
							<input type="text" class="form-control" value="{{planificacion.tipo_materia_prima.nombre}}" disabled>
						</div>
						<div class="form-group col-md-4">
							<label>Especie</label>
							<input type="text" class="form-control" value="{{planificacion.especie.nombre}}" disabled>
						</div>
						<div class="form-group col-md-4">
							<label>Restricción</label>
							{{#if planificacion.tiene_restriccion}}
								<input type="text" class="form-control" value="Sí" disabled>
							{{else}}
								<input type="text" class="form-control" value="No" disabled>
							{{/if}}
						</div>
					</div>
				</div>
				<div class="bg-light border rounded p-3 my-3">
					<h5>Transporte desde XII Región a X Región</h5>
					<div class="row my-3">
						<div class="form-group col-md-4">
							<label>Tipo de Transporte</label>
							<input type="text" class="form-control" value="{{planificacion.tipo_transporte.nombre}}" disabled>
						</div>
						{{#if conCabotaje}}
							<div class="form-group col-md-4">
								<label>Fecha de inicio transporte maritimo</label>
								<input type="text" class="form-control" value="{{planificacion.fecha_embarque_format}}" disabled>
							</div>
							<div class="form-group col-md-4">
								<label>Fecha de arribo a puerto de Puerto Montt</label>
								<input type="text" class="form-control" value="{{planificacion.fecha_arribo_puerto_format}}" disabled>
							</div>
						{{/if}}
					</div>
					<div class="row my-3">
						<div class="form-group col-md-4">
							<label>Rampla con que se retira desde Planta</label>
							<input type="text" class="form-control" value="{{planificacion.rampla.patente}}" disabled>
						</div>
						<div class="form-group col-md-4">
							<label>Estado de Rampla</label>
							<input type="text" class="form-control" value="{{planificacion.estado_rampla.nombre}}" disabled>
						</div>
						<div class="form-group col-md-4">
							<label>Camión que retira desde Planta</label>
							<input type="text" class="form-control" value="{{planificacion.camion.patente}}" disabled>
						</div>
					</div>
					<div class="row my-3">
						<div class="form-group col-md-4">
							<label>Transportista</label>
							<input type="text" class="form-control" value="{{planificacion.camion.empresa.razon_social}}" disabled>
						</div>
						<div class="form-group col-md-4">
							<label>Tipo de Camión</label>
							<input type="text" class="form-control" value="{{planificacion.camion.tipo_camion.nombre}}" disabled>
						</div>
						<div class="form-group col-md-4">
							<label>Conductor que retira desde Planta</label>
							<input type="text" class="form-control" value="{{planificacion.conductor.nombre}} {{planificacion.conductor.apellido}}" disabled>
						</div>
					</div>
				</div>

				{{#if conRescate}}
					<div class="bg-light border rounded p-3 my-3">
						<h5>Rescate desde puerto de Puerto Montt</h5>
						<div class="row my-3">
							<div class="form-group col-md-4">
								<label>Fecha de rescate desde puerto</label>
								<input type="text" class="form-control" value="{{planificacion.fecha_rescate_puerto_format}}" disabled>
							</div>
							<div class="form-group col-md-4">
								<label>Camión de Rescate (Puerto -> Planta)</label>
								<input type="text" class="form-control" value="{{planificacion.camion_rescate_patente}}" disabled>
							</div>
							<div class="form-group col-md-4">
								<label>Conductor que rescata desde puerto</label>
								<input type="text" class="form-control" value="{{planificacion.conductor_rescate_nombre}}" disabled>
							</div>
						</div>
					</div>
				{{/if}}
			{{/if}}

		{{else}}

			{{#if noEstadoAceptado}}
				<div class="bg-light border rounded p-3">
					<h5>Datos de la Planificación de la Reposición</h5>
					<div class="row">
						<div class="form-group col-md-4">
							<label>Fecha y horario planificado</label>
							<input type="datetime-local" class="form-control" value="{{planificacion.fecha_hora_planificada_format}}" disabled>
						</div>
						<div class="form-group col-md-4">
						</div>
						<div class="form-group col-md-4">
						</div>
					</div>
				</div>
			{{/if}}

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
