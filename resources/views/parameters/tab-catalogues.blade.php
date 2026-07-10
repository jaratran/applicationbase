<h4 class="mb-4">Mantenedor de Catálogos</h4>

<div class="row">
    <!-- Columna izquierda: Lista de categorías -->
    <div class="col-md-4">
        <div class="list-group" id="lista-categorias">
            @foreach($categorias as $categoria)
                <button type="button"
                        class="list-group-item list-group-item-action no-guard"
                        data-id="{{ $categoria->id }}">
                    {{ $categoria->nombre }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Columna derecha: Valores de la categoría seleccionada -->
    <div class="col-md-8">
        <div id="panel-valores" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 id="titulo-categoria" class="mb-0">Valores de la categoría</h5>
                <button type="button" id="btn-agregar" class="btn btn-sm btn-success no-guard">
                    <i class="fas fa-plus"></i> Agregar Valor
                </button>
            </div>

            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th>Nombre</th>
                        <th style="width: 10%">Activo</th>
                        <th style="width: 10%">Orden</th>
                        <th style="width: 20%">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-valores">
                    {{-- Aquí se insertan dinámicamente los valores --}}
                </tbody>
            </table>
        </div>
    </div>
</div>