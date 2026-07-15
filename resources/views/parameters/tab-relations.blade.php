<h4 class="mb-4">Gestión de Relaciones entre Valores de Catálogo</h4>

<div class="row">
    <!-- Columna izquierda: Tipos de relación -->
    <div class="col-md-3">
        <h6>Tipos de Relación</h6>
        <div class="list-group" id="lista-tipos-relacion">
            @foreach($tiposRelacion as $tipo)
                <button type="button"
                        class="list-group-item list-group-item-action no-guard"
                        data-id="{{ $tipo->id }}">
                    {{ $tipo->nombre }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Columna centro: Valores Agrupadores -->
    <div class="col-md-4">
        <div id="panel-agrupadores" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 id="titulo-agrupadores" class="mb-0">Valores Agrupadores</h6>
            </div>

            <!-- Select categoría de agrupadores -->
            <div id="seleccion-categoria-origen" class="mb-2">
                <label class="form-label small">Seleccione categoría de agrupadores</label>
                <select class="form-select form-select-sm no-guard" id="select-categoria-origen">
                    <option value="">-- Seleccione --</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Botón Confirmar / Liberar Categoría -->
            <div class="d-flex justify-content-end mb-2">
                <button id="btn-confirmar-categoria"
                        type="button"
                        class="btn btn-sm btn-primary no-guard"
                        data-modo="confirmar"
                        data-label-confirmar="Confirmar Categoría"
                        data-label-liberar="Liberar Categoría"
                        data-bloqueado="false"
                        disabled>
                    Confirmar Categoría
                </button>
            </div>

            <ul class="list-group" id="lista-agrupadores">
                {{-- Se cargan dinámicamente --}}
            </ul>
        </div>
    </div>

    <!-- Columna derecha: Valores Subordinados -->
    <div class="col-md-5">
        <div id="panel-subordinados" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 id="titulo-subordinados" class="mb-0">Valores Subordinados</h6>
            </div>


            <!-- Select categoría de subordinados -->
            <div id="seleccion-categoria-destino" class="mb-2">
                <label class="form-label small">Seleccione categoría de subordinados</label>
                <select class="form-select form-select-sm no-guard" id="select-categoria-destino">
                    <option value="">-- Seleccione --</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Botón Confirmar / Liberar Categoría de Subordinados -->
            <div class="d-flex justify-content-end mb-2">
                <button id="btn-confirmar-categoria-destino"
                        type="button"
                        class="btn btn-sm btn-primary no-guard"
                        data-modo="confirmar"
                        data-label-confirmar="Confirmar Categoría"
                        data-label-liberar="Liberar Categoría"
                        data-bloqueado="false"
                        disabled>
                    Confirmar Categoría
                </button>
            </div>

            <ul class="list-group" id="lista-subordinados">
                {{-- Se insertan dinámicamente con JS --}}
            </ul>
        </div>
    </div>
</div>
