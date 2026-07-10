<script id="template-vincula-plantas" type="text/x-handlebars-template">
@verbatim
    <div class="bg-light border rounded p-3 mb-4">
        <h5>Información General</h5>
        <div class="row">
            <div class="form-group col-md-6">
                <label>RUT</label>
                <input type="text" class="form-control" value="{{empresa.rut_empresa}}" disabled>
            </div>
            <div class="form-group col-md-6">
                <label>Razón Social</label>
                <input type="text" class="form-control" value="{{empresa.razon_social}}" disabled>
            </div>
        </div>
    </div>

    <div class="bg-light border rounded p-3 mb-4">
        <h5>Asociación con Plantas de Proceso</h5>
        <!-- Bloque condicional para Transportista -->
        <div id="asociacion-plantas-wrapper" class="mt-3">
            <label>Asociar Plantas de Proceso (Sucursales)</label>
            <div class="d-flex gap-2 mb-2">
                <select class="form-control select2" id="plantaProceso_id" name="plantaProceso_id" style="width: 100%;">
                    <option value="">Seleccione planta ...</option>
                </select>
                <button type="button" class="btn btn-success" id="btn-agregar-plantaProceso">Agregar</button>
            </div>

            <ul id="plantaProceso-asociadas" class="list-group mb-3">
                <!-- plantaProcesos agregadas dinámicamente -->
            </ul>
        </div>

        <!-- Campo oculto que se va llenando con los IDs seleccionados -->
        <div id="contenedor-hidden" style="display: none;"></div>
    </div>
@endverbatim
</script>