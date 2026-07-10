<script id="template-vincula-productoras" type="text/x-handlebars-template">
@verbatim
    <div class="bg-light border rounded p-3 mb-4">
        <h5>Información General</h5>
        <div class="row">
            <div class="form-group col-md-6">
                <label>Nombre de Planta</label>
                <input type="text" class="form-control" value="{{sucursal.nombre_sucursal}}" disabled>
            </div>
            <div class="form-group col-md-6">
                <label>Código SIEP</label>
                <input type="text" class="form-control" value="{{sucursal.codigo_siep}}" disabled>
            </div>
        </div>
    </div>

    <div class="bg-light border rounded p-3 mb-4">
        <h5>Asociación con Productoras de Materia Prima</h5>
        <!-- Bloque condicional para Planta de Proceso -->
        <div id="asociacion-productoras-wrapper" class="mt-3">
            <label>Asociar Productoras de Materia Prima (Empresas)</label>
            <div class="d-flex gap-2 mb-2">
                <select class="form-control select2" id="productoraMMPP_id" name="productoraMMPP_id" style="width: 100%;">
                    <option value="">Seleccione productora ...</option>
                </select>
                <button type="button" class="btn btn-success" id="btn-agregar-productoraMMPP">Agregar</button>
            </div>

            <ul id="productoraMMPP-asociadas" class="list-group mb-3">
                <!-- productoraMMPPs agregadas dinámicamente -->
            </ul>
        </div>

        <!-- Campo oculto que se va llenando con los IDs seleccionados -->
        <div id="contenedor-hidden" style="display: none;"></div>
    </div>
@endverbatim
</script>