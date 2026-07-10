<script id="template-show-sucursal" type="text/x-handlebars-template">
@verbatim
    <div class="bg-light border rounded p-3 mb-4">
        <h5>Información de la Sucursal</h5>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Nombre</label>
                <input type="text" class="form-control" value="{{nombre_sucursal}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Código Siep</label>
                <input type="text" class="form-control" value="{{codigo_siep}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Tipo de Sucursal</label>
                <input type="text" class="form-control" value="{{tipo_sucursal.nombre}}" disabled>
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group col-md-4">
                <label>Email</label>
                <input type="text" class="form-control" value="{{email}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Teléfono</label>
                <input type="text" class="form-control" value="{{telefono}}" disabled>
            </div>
        </div>
    </div>

    <div class="bg-light border rounded p-3 mb-4">
        <h5>Ubicación y Detalles</h5>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Zona</label>
                <input type="text" class="form-control" value="{{zona.nombre}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Comuna</label>
                <input type="text" class="form-control" value="{{comuna.nombre}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Region</label>
                <input type="text" class="form-control" value="{{comuna.region.nombre}}" disabled>
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group col-md-4">
                <label>Kilómetros</label>
                <input type="text" class="form-control" value="{{km}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Tiempo Estimado</label>
                <input type="text" class="form-control" value="{{tiempo_estimado_viaje}}" disabled>
            </div>
        </div>
    </div>

    {{#unless activo}}
        <div class="bg-light border rounded p-3 mb-4">
            <h5>Observaciones</h5>
            <div class="form-group col-md-12">
                <label>Observación de Inactividad</label>
                <textarea class="form-control" rows="2" disabled>{{observacion_inactividad}}</textarea>
            </div>
        </div>
    {{/unless}}

    <div class="bg-light border rounded p-3">
        <h5 class="text-center mb-4">Productoras de Materia Prima Atendidas</h5>
        {{#if empresas_atendidas.length}}
            <ul class="list-group list-group-flush">
                {{#each empresas_atendidas}}
                    <li class="list-group-item">
                        <div class="fw-bold mb-1">{{razon_social}}</div>
                        <small class="d-block text-muted">RUT: {{rut_empresa}}</small>
                        <small class="d-block text-muted">Teléfono: {{telefono}}</small>
                        <small class="d-block text-muted">Dirección: {{direccion}}</small>
                        <small class="d-block text-muted">Comuna: {{comuna.nombre}}</small>
                        <small class="d-block text-muted">Región: {{comuna.region.nombre}}</small>
                    </li>
                {{/each}}
            </ul>
        {{else}}
            <p class="text-muted text-center mb-0">Esta sucursal no tiene empresas asociadas actualmente.</p>
        {{/if}}
    </div>
@endverbatim
</script>
