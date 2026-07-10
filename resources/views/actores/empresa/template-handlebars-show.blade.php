<script id="template-show-empresa" type="text/x-handlebars-template">
@verbatim
    <div class="bg-light border rounded p-3 mb-4">
        <h5>Información General</h5>
        <div class="row">
            <div class="form-group col-md-4">
                <label>RUT</label>
                <input type="text" class="form-control" value="{{rut_empresa}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Razón Social</label>
                <input type="text" class="form-control" value="{{razon_social}}" disabled>
            </div>
        </div>
    </div>

    <div class="bg-light border rounded p-3 mb-4">
        <h5>Contacto y Ubicación</h5>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Correo de Contacto</label>
                <input type="text" class="form-control" value="{{email_contacto}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Teléfono de Contacto</label>
                <input type="text" class="form-control" value="{{telefono_contacto}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label>Teléfono General</label>
                <input type="text" class="form-control" value="{{telefono}}" disabled>
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group col-md-4">
                <label>Dirección</label>
                <input type="text" class="form-control" value="{{direccion}}" disabled>
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
        <h5 class="text-center mb-4">Plantas Procesadoras Asociadas</h5>
        {{#if plantas_procesadoras.length}}
            <ul class="list-group list-group-flush">
                {{#each plantas_procesadoras}}
                    <li class="list-group-item">
                        <div class="fw-bold mb-1">{{nombre_sucursal}}</div>
                        <small class="d-block text-muted">Código Siep: {{codigo_siep}}</small>
                        <small class="d-block text-muted">Teléfono: {{telefono}}</small>
                        <small class="d-block text-muted">Comuna: {{comuna.nombre}}</small>
                        <small class="d-block text-muted">Región: {{comuna.region.nombre}}</small>
                    </li>
                {{/each}}
            </ul>
        {{else}}
            <p class="text-muted text-center mb-0">Esta empresa no tiene plantas procesadoras asociadas actualmente.</p>
        {{/if}}
    </div>
@endverbatim
</script>
