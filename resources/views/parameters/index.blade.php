@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white fs-5"> Parámetros Generales </div>
                <form action="{{ route('parameters.update') }}" method="POST" id="form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="card-body">
                        @include('includes.alertas-sistema')
                                            
						<ul class="nav nav-tabs mb-3" id="paramTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active no-guard" id="design-tab" data-bs-toggle="tab" data-bs-target="#design" type="button" role="tab" aria-controls="design" aria-selected="true">Parámetros de Diseño</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link no-guard" id="operacionales-tab" data-bs-toggle="tab" data-bs-target="#operationals" type="button" role="tab" aria-controls="operationals"> Parámetros Operacionales </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link no-guard" id="catalogues-tab" data-bs-toggle="tab" data-bs-target="#catalogues" type="button" role="tab">Catálogos</button>
                            </li>
<!--
                            <li class="nav-item" role="presentation">
                                <button class="nav-link no-guard" id="relations-tab" data-bs-toggle="tab" data-bs-target="#relations" type="button" role="tab">Relaciones de Catálogo</button>
                            </li>
-->
                        </ul>

						<div class="tab-content" id="paramTabsContent">
                            <div class="tab-pane fade show active" id="design" role="tabpanel">
								@include('parameters.tab-design')
							</div>
							<div class="tab-pane fade" id="operationals" role="tabpanel">
								@include('parameters.tab-operationals')
							</div>
                            <div class="tab-pane fade" id="catalogues" role="tabpanel">
                                @include('parameters.tab-catalogues')
                            </div>
                            <div class="tab-pane fade" id="relations" role="tabpanel">
                                @include('parameters.tab-relations')
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary my-2 no-guard">
                            <i class="fa fa-floppy-o"></i> Guardar Cambios
						</button>

                        <button type="button" class="btn btn-secondary my-2 no-guard" onclick="window.location.href='{{ url('panel') }}'">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE ADVERTENCIA DE CAMBIOS NO GUARDADOS -->
<div class="modal fade" id="modalSalidaNoGuardada" tabindex="-1" aria-labelledby="modalSalidaNoGuardadaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalSalidaNoGuardadaLabel">Cambios no guardados</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 4rem;"></i>
                <p class="fs-5">Ha realizado cambios en los parámetros.</p>
                <p class="text-muted small mb-0">¿Está seguro de salir sin guardar?</p>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary no-guard" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>

                <button type="button" class="btn btn-warning no-guard" id="confirmarSalida">Salir de todos modos</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    <!-- Script para recordar el último tab activo -->        
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Al hacer click en cualquier tab
            const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabButtons.forEach(function (btn) {
                btn.addEventListener('shown.bs.tab', function (event) {
                    sessionStorage.setItem('lastActiveTab', event.target.getAttribute('data-bs-target'));
                });
            });

            // Al cargar la página, recuperar el último tab
            const lastTab = sessionStorage.getItem('lastActiveTab');
            if (lastTab) {
                const triggerEl = document.querySelector(`button[data-bs-target="${lastTab}"]`);
                if (triggerEl) {
                    new bootstrap.Tab(triggerEl).show();
                }
            }
        });
    </script>

    <!-- Otorgar valor color standard Bootstrap5 a cada categoria de color -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btnResetColor').forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const defaultColor = this.getAttribute('data-color');
                    const inputColor = document.getElementById(targetId);

                    // Cambia el valor
                    inputColor.value = defaultColor;

                    // Despacha evento change para que se active el guardia
                    const changeEvent = new Event('change', { bubbles: true });
                    inputColor.dispatchEvent(changeEvent);
                });
            });
        });
    </script>

    <!-- Script para prevenir que el Enter dispare el submit en toda la vista -->
    <script>
	    // Prevenir que Enter dispare el submit del formulario en cualquier input
	    document.querySelector('#form').addEventListener('keydown', function (e) {
	        const isEnter = e.key === 'Enter';
	        const isTextarea = e.target.tagName === 'TEXTAREA';
	        if (isEnter && !isTextarea) {
	            e.preventDefault();
	        }
	    });
	</script>
    <!-- Captura del Submit para colocar Spinner en el Botón y evitar doble Submit -->
    <script>
		$('#form').on('submit', function (e) {
			// 🔁 Controlar doble submit - Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
			if (this.enviado) {
				console.log("⛔ Doble submit detectado, se detiene.");
				e.preventDefault();
				return;
			}

            // Deshabilita el botón para evitar segundo click
			const $btnSubmit = $('#form button[type="submit"]');
			if ($btnSubmit.length) {
			    $btnSubmit.prop('disabled', true);
			    $btnSubmit.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
			}

			// 🧷 Flag para evitar siguiente submit
			this.enviado = true;
		});
    </script>

    <!-- Bloque con Script para manejar la lógica de los catálogos -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categorias = @json($categorias);
            const catalogosIniciales = @json($valoresCatalogo);

            const categoriaList = document.getElementById('lista-categorias');
            const valoresPanel = document.getElementById('panel-valores');
            const valoresTable = document.getElementById('tabla-valores');
            const tituloCategoria = document.getElementById('titulo-categoria');
            const form = document.getElementById('form');

            let categoriaSeleccionadaId = null;
            window.catalogos = [...catalogosIniciales]; // Para que esté disponible globalmente y Trabajamos sobre esta copia
            window.catalogosOriginal = JSON.stringify(window.catalogos); // Guardamos su estado original (catalogosOriginal) una vez poblado 'catalogos' ← Agregado por corrección 12-06-25. 

            // Crear input oculto para mantener los valores de catálogo actualizados
            let inputCatalogos = document.createElement('input');
            inputCatalogos.type = 'hidden';
            inputCatalogos.name = 'catalogos';
            form.appendChild(inputCatalogos);

            // Actualiza el input oculto cada vez que cambian los valores
            function sincronizarCatalogos() {
                inputCatalogos.value = JSON.stringify(
                    catalogos.filter(item => item.catalogo_id !== null)
                );
            }

            // Mostrar valores correspondientes a la categoría seleccionada
            function mostrarValoresDeCategoria() {
                valoresTable.innerHTML = '';

                const valores = catalogos.filter(c => c.catalogo_id === categoriaSeleccionadaId);
                valores.sort((a, b) => a.orden - b.orden);

                valores.forEach((valor, index) => {
                    const fila = construirFila(valor, index + 1);
                    valoresTable.appendChild(fila);
                });

                actualizarOrdenes();
            }

            // Construye una fila editable
            function construirFila(valor, pos) {
                const tr = document.createElement('tr');
                tr.dataset.id = valor.id ?? '';
                tr.dataset.uid = valor.__uid ?? ''; // Resguardamos el uID por si se trata de un elemento creado en el front (id=null)

                tr.innerHTML = `
                    <td class="text-center">${pos}</td>
                    <td><input type="text" class="form-control form-control-sm nombre" value="${valor.nombre ?? ''}" /></td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input activo" ${valor.activo ? 'checked' : ''} />
                    </td>
                    <td class="text-center orden">${valor.orden}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-subir no-guard" title="Subir fila"><i class="fas fa-arrow-up"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-bajar no-guard" title="Bajar fila"><i class="fas fa-arrow-down"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar no-guard" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;

                // Actualizar en tiempo real los cambios
                tr.querySelector('.nombre').addEventListener('input', e => {
                    valor.nombre = e.target.value;
                    sincronizarCatalogos();
                });

                tr.querySelector('.activo').addEventListener('change', e => {
                    valor.activo = e.target.checked;
                    sincronizarCatalogos();
                });

                tr.querySelector('.btn-subir').addEventListener('click', () => moverFila(tr, -1));
                tr.querySelector('.btn-bajar').addEventListener('click', () => moverFila(tr, 1));
                tr.querySelector('.btn-eliminar').addEventListener('click', () => {
                    tr.remove();
                    const idx = catalogos.indexOf(valor);
                    if (idx !== -1) catalogos.splice(idx, 1);
                    actualizarOrdenes();
                    sincronizarCatalogos();
                });

                return tr;
            }

            // Orden visual y actualización de campo .orden en el modelo
            function actualizarOrdenes() {
                const filas = document.querySelectorAll('#tabla-valores tr');

                filas.forEach((tr, idx) => {
                    tr.querySelector('.orden').textContent = idx + 1;

                    let valor =  null;

                    const id = tr.dataset.id || null;
                    const uid = tr.dataset.uid || null;

                    if(id !== null){
                        valor = catalogos.find(c => c.id    ==  id);     // Lo busca entre los recibidos desde el back (id distinto de nulo)
                    }
                    else{
                        valor = catalogos.find(c => c.__uid === uid);    // Lo busca entre los creados en el front (id igual a nulo)
                    }

                    if (valor) valor.orden = idx + 1;

                    // Desactivar botón subir si es la primera fila
                    const btnSubir = tr.querySelector('.btn-subir');
                    btnSubir.disabled = idx === 0;

                    // Desactivar botón bajar si es la última fila
                    const btnBajar = tr.querySelector('.btn-bajar');
                    btnBajar.disabled = idx === filas.length - 1;
                });

                sincronizarCatalogos();
            }

            function moverFila(fila, direccion) {
                const tbody = fila.parentElement;
                const filas = Array.from(tbody.children);
                const index = filas.indexOf(fila);
                const nuevoIndex = index + direccion;

                if (nuevoIndex >= 0 && nuevoIndex < filas.length) {
                    if (direccion < 0) {
                        tbody.insertBefore(fila, filas[nuevoIndex]);
                    } else {
                        tbody.insertBefore(filas[nuevoIndex], fila);
                    }
                    actualizarOrdenes();
                }
            }

            // Al seleccionar categoría
            categoriaList.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', () => {
                    categoriaSeleccionadaId = parseInt(btn.dataset.id);
                    tituloCategoria.textContent = categorias.find(c => c.id === categoriaSeleccionadaId).nombre;
                    mostrarValoresDeCategoria();
                    valoresPanel.classList.remove('d-none');
                });
            });

            // Agregar nuevo valor
            document.getElementById('btn-agregar').addEventListener('click', () => {
                const nuevo = {
                    id: null,
                    __uid: crypto.randomUUID(), // Identificador único para elementos generados en el front (con id = null)
                    nombre: '',
                    activo: true,
                    orden: catalogos.filter(c => c.catalogo_id === categoriaSeleccionadaId).length + 1,
                    catalogo_id: categoriaSeleccionadaId
                };

                catalogos.push(nuevo);
                const fila = construirFila(nuevo, nuevo.orden);
                valoresTable.appendChild(fila);
                actualizarOrdenes();
            });

            sincronizarCatalogos(); // por si vienen datos ya cargados
        });
    </script>

    <!-- Bloque con Script para manejar la lógica de las relaciones de catálogos -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const catalogos = @json($valoresCatalogo);
            const relacionesIniciales = @json($relaciones);

            const listaTiposRelacion = document.getElementById('lista-tipos-relacion');
            const listaAgrupadores = document.getElementById('lista-agrupadores');
            const listaSubordinados = document.getElementById('lista-subordinados');
            const panelAgrupadores = document.getElementById('panel-agrupadores');
            const panelSubordinados = document.getElementById('panel-subordinados');
            const selectCategoriaOrigen = document.getElementById('select-categoria-origen');
            const selectCategoriaDestino = document.getElementById('select-categoria-destino');
            const btnConfirmar = document.getElementById('btn-confirmar-categoria');
            const btnConfirmarDestino = document.getElementById('btn-confirmar-categoria-destino');
            const form = document.getElementById('form');

            let tipoRelacionSeleccionado = null;
            window.relacionesData = [...relacionesIniciales]; // Para que esté disponible globalmente y Trabajamos sobre esta copia

            // Crear input oculto para mantener los valores de relación actualizados
            const inputRelaciones = document.createElement('input');
            inputRelaciones.type = 'hidden';
            inputRelaciones.name = 'relaciones';
            form.appendChild(inputRelaciones);

            let agrupadorActivo = null;
            let categoriaOrigen = null;
            let categoriaDestino = null;

            if (!btnConfirmar || !btnConfirmarDestino ) return;
            if (!catalogos.length) return;

            // Cuando se selecciona tipo de relación
            listaTiposRelacion.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', () => {
                    // Ajustamos colores para destacar la relación activa
                    listaTiposRelacion.querySelectorAll('button').forEach(b => {
                        b.classList.remove('active', 'bg-primary', 'text-white');
                    });
                    btn.classList.add('active', 'bg-primary', 'text-white');

                    agrupadorActivo = null;
                    tipoRelacionSeleccionado = parseInt(btn.dataset.id);

                    panelAgrupadores.classList.remove('d-none');
                    panelSubordinados.classList.add('d-none');

                    const relacionesExistentes = relacionesData.filter(r => r.tipo_relacion_id === tipoRelacionSeleccionado && r.valor_destino_id !== null);

                    if( relacionesExistentes.length > 0) {
                        // Buscamos el primer agrupador y subordinado para obtener sus respectivas categorias
                        const primerAgrupador_id = relacionesExistentes[0]?.valor_origen_id;
                        const primerSubordinado_id = relacionesExistentes[0]?.valor_destino_id;
                        const primerAgrupador = catalogos.find(c => c.id === primerAgrupador_id);
                        const primerSubordinado = catalogos.find(c => c.id === primerSubordinado_id);

                        // Agrupador: Establecemos el Selector y Boton modo a Liberar
                        categoriaOrigen = primerAgrupador.catalogo_id?.toString() ?? '';
                        selectCategoriaOrigen.value = categoriaOrigen;
                        selectCategoriaOrigen.setAttribute('disabled', 'disabled');

                        btnConfirmar.innerText = btnConfirmar.dataset.labelLiberar;
                        btnConfirmar.dataset.modo = 'liberar';

                        // Subordinado: Establecemos el Selector y Boton modo a Liberar
                        categoriaDestino = primerSubordinado.catalogo_id?.toString() ?? '';
                        selectCategoriaDestino.value = categoriaDestino;
                        selectCategoriaDestino.setAttribute('disabled', 'disabled');

                        btnConfirmarDestino.innerText = btnConfirmarDestino.dataset.labelLiberar;
                        btnConfirmarDestino.dataset.modo = 'liberar';

                    } else { // No hay relaciones existentes
                        // Agrupador : DesEstablecemos el Selector y Boton modo a Confirmar
                        categoriaOrigen = null;
                        selectCategoriaOrigen.value = '';
                        selectCategoriaOrigen.removeAttribute('disabled');

                        btnConfirmar.innerText = btnConfirmar.dataset.labelConfirmar;
                        btnConfirmar.dataset.modo = 'confirmar';

                        // Subordinado: DesEstablecemos el Selector y Boton modo a Confirmar pero Disabled - El selector cuando seleccione lo activa
                        categoriaDestino = null;
                        selectCategoriaDestino.value = '';
                        selectCategoriaDestino.removeAttribute('disabled');

                        btnConfirmarDestino.innerText = btnConfirmarDestino.dataset.labelConfirmar;
                        btnConfirmarDestino.dataset.modo = 'confirmar';
                        btnConfirmarDestino.setAttribute('disabled', 'disabled');
                    }

                    renderAgrupadores();
                });
            });

            // Mostrar agrupadores según tipo
            function renderAgrupadores() {
                listaAgrupadores.innerHTML = '';

                const catIdOrigen = parseInt(categoriaOrigen);
                const agrupadoresPosibles = catalogos.filter(c => c.catalogo_id === catIdOrigen);

                if (agrupadoresPosibles.length > 0) {
                    const algunoConSubordinados = relacionesData.some(r =>
                        r.tipo_relacion_id === tipoRelacionSeleccionado &&
                        r.valor_origen_id &&
                        r.valor_destino_id
                    );

                    if (algunoConSubordinados) {
                        // Deshabilitamos el botón hasta que no existan subordinados
                        btnConfirmar.setAttribute('disabled', 'disabled');
                    } else {
                        // Como no hay subordinados -> Habilitamos el botón
                        btnConfirmar.removeAttribute('disabled');
                    }

                    agrupadoresPosibles.forEach(agrupadorPosible => {
                        const cantidadSubordinados = relacionesData.filter(r =>
                            r.tipo_relacion_id === tipoRelacionSeleccionado &&
                            r.valor_origen_id === agrupadorPosible.id &&
                            r.valor_destino_id !== null
                        ).length;

                        const li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action no-guard';
                        li.dataset.id = agrupadorPosible.id;
                        li.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <span>${agrupadorPosible.nombre}</span>
                                ${cantidadSubordinados > 0 ? `<span class="badge bg-primary rounded-pill">${cantidadSubordinados}</span>` : ''}
                            </div>
                        `;

                        // 🟦 Si este agrupador está activo → aplicar estilo activo
                        if (agrupadorPosible.id === agrupadorActivo) {
                            li.classList.add('active', 'bg-primary', 'text-white');

                            // 🔄 Localizar el badge dentro del li (si existe) y sobreescribir su clase en 'video inverso'
                            const badge = li.querySelector('.badge');
                            if (badge) {
                                badge.className = 'badge bg-white text-primary rounded-pill';
                            }
                        }

                        li.addEventListener('click', function (e) {
                            agrupadorActivo = agrupadorPosible.id;

                            // Marcar visualmente solo este como activo
                            listaAgrupadores.querySelectorAll('li').forEach(el => el.classList.remove('active', 'bg-primary', 'text-white'));
                            li.classList.add('active', 'bg-primary', 'text-white');                            

                            // Resetear todos los badges al estilo normal ...
                            listaAgrupadores.querySelectorAll('.badge').forEach(badge => {
                                badge.className = 'badge bg-primary text-white rounded-pill';
                            });
                            // ... y cambiar el badge del agrupador activo a 'video inverso'
                            const badgeActivo = li.querySelector('.badge');
                            if (badgeActivo) {
                                badgeActivo.className = 'badge bg-white text-primary rounded-pill';
                            }

                            renderSubordinados();
                            panelSubordinados.classList.remove('d-none');
                        });

                        listaAgrupadores.appendChild(li);
                    });
                }
            }

            // Mostrar subordinados de agrupador activo
            function renderSubordinados() {
                listaSubordinados.innerHTML = '';
                const catIdDestino = parseInt(categoriaDestino);
                const subordinadosPosibles = catalogos.filter(c => c.catalogo_id === catIdDestino);

                subordinadosPosibles.forEach((valor) => {
                    // Buscar si el subordinado ya está relacionado con ALGÚN agrupador en ESTA relación
                    const relacionGeneral = relacionesData.find(r =>
                                                                        r.tipo_relacion_id === tipoRelacionSeleccionado &&
                                                                        r.valor_destino_id === valor.id
                                                                    );

                    // Buscar si el subordinado está relacionado con ESTE agrupador
                    const relacionConActual = relacionesData.find(r =>
                                                                        r.tipo_relacion_id === tipoRelacionSeleccionado &&
                                                                        r.valor_origen_id === agrupadorActivo &&
                                                                        r.valor_destino_id === valor.id
                                                                    );

                    const estaConOtroAgrupador = relacionGeneral && !relacionConActual;

                    const li = document.createElement('li');
                    li.className = `list-group-item d-flex justify-content-between align-items-center no-guard ${estaConOtroAgrupador ? 'text-muted bg-light opacity-50' : ''}`;
                    li.dataset.id = valor.id;
                    li.innerHTML = `    <div>${valor.nombre}</div>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="form-check form-switch mx-2">
                                                <input class="form-check-input toggle-activo no-guard"
                                                    type="checkbox"
                                                    ${relacionConActual ? 'checked' : ''}
                                                    ${estaConOtroAgrupador ? 'disabled' : ''}>
                                            </div>                                                    
                                        </div>
                                    `;

                    // Solo si el subordinado está libre el Toggle está permitido (NO cuando está con otro Agrupador)
                    if (!estaConOtroAgrupador) {
                        li.querySelector('.toggle-activo')?.addEventListener('change', function () {
                            const yaExiste = relacionConActual;

                            if (this.checked && !yaExiste) {    // Check y NO existe -> Lo agregamos ...
                                relacionesData.push({
                                                    id: null,
                                                    valor_origen_id: agrupadorActivo,
                                                    valor_destino_id: valor.id,
                                                    tipo_relacion_id: tipoRelacionSeleccionado
                                                });
                            }
                            else if (!this.checked && yaExiste) { // Check y SI existe -> Lo sacamos
                                relacionesData = relacionesData.filter(r =>
                                                                            !(r.tipo_relacion_id === tipoRelacionSeleccionado &&
                                                                            r.valor_origen_id === agrupadorActivo &&
                                                                            r.valor_destino_id === valor.id)
                                                                        );
                            }
        
                            // Cada vez que se toggle copiamos en la variable oculta las relaciones ...
                            inputRelaciones.value = JSON.stringify(
                                                                    relacionesData.filter(r => r.valor_destino_id !== null) // El practica son todos los registros porque ninguno ha de tener destino=null
                                                                );

                            renderSubordinados();               // ... y renderizaramos para que el Botón se refresque
                            renderAgrupadores();                // ... y también los agrupadores por los badges
                        });
                    }

                    listaSubordinados.appendChild(li);
                });

                // Verificar si hay vinculados para Habilitar o Deshabilitar o no el Botón
                const hayVinculados = relacionesData.some(r => r.tipo_relacion_id === tipoRelacionSeleccionado);
                if (hayVinculados) {
                    btnConfirmarDestino.setAttribute('disabled', 'disabled');
                }
                else {
                    if (!isNaN(catIdDestino)) {
                        btnConfirmarDestino.removeAttribute('disabled');                                                                            
                    }
                }
            }

            // Cuando se setee la categoría origen -> Su Botón debe estar activo
            selectCategoriaOrigen.addEventListener('change', function () {
                btnConfirmar.removeAttribute('disabled');
            });

            // Cuando se setee la categoría destino -> Su Botón debe estar activo
            selectCategoriaDestino.addEventListener('change', function () {
                btnConfirmarDestino.removeAttribute('disabled');
            });

            // Acción del botón Confirmar / Liberar Categoría de Agrupadores
            btnConfirmar.addEventListener('click', function () {
                if (this.dataset.modo === 'confirmar') {
                    // Seteamos CategoriaOrigen, Deshabilitamos el Selector, Botón a modo "liberar" ...
                    categoriaOrigen = selectCategoriaOrigen.value;
                    selectCategoriaOrigen.setAttribute('disabled', 'disabled');
                    btnConfirmar.innerText = btnConfirmar.dataset.labelLiberar;
                    btnConfirmar.dataset.modo = 'liberar';

                    // ...y Renderizamos la lista de agrupadores, unSet selector de Subordinados
                    renderAgrupadores();
                    selectCategoriaDestino.value = '';
                }
                else { // Modo Liberar
                    // UnSeteamos CategoriaOrigen, Habilitamos el Selector y Botón a modo "Confimar" ...
                    categoriaOrigen = null;
                    selectCategoriaOrigen.removeAttribute('disabled');
                    btnConfirmar.innerText = btnConfirmar.dataset.labelConfirmar;
                    btnConfirmar.dataset.modo = 'confirmar';

                    // ... y Limpiamos la lista de agrupadores
                    listaAgrupadores.innerHTML = '';

                    // ... y Ocultamos panel de Subordinados (se mostrará al seleccionar un agrupador)
                    panelSubordinados.classList.add('d-none');
                }
            });

            // Acción del botón Confirmar / Liberar Categoría de Subordinados
            btnConfirmarDestino.addEventListener('click', function () {
                if (this.dataset.modo === 'confirmar') {
                    // Deshabilitamos el botón de Agrupadores hasta que libere Categoría de Subordinados
                    btnConfirmar.setAttribute('disabled', 'disabled');

                    // Seteamos CategoriaDestino, Deshabilitamos el Selector y Botón a modo "liberar"  ...
                    categoriaDestino = selectCategoriaDestino.value;
                    selectCategoriaDestino.setAttribute('disabled', 'disabled');
                    btnConfirmarDestino.innerText = btnConfirmarDestino.dataset.labelLiberar;
                    btnConfirmarDestino.dataset.modo = 'liberar';                    

                    // ... y Renderizamos la lista de subordinados
                    renderSubordinados();

                }
                else { // Modo Liberar
                    // UnSeteamos CategoriaDestino, Habilitamos el Selector y boton a modo "Confimar" ...
                    categoriaDestino = null;
                    selectCategoriaDestino.removeAttribute('disabled'); // Habilitar el selector de categoría
                    btnConfirmarDestino.innerText = btnConfirmarDestino.dataset.labelConfirmar;
                    btnConfirmarDestino.dataset.modo = 'confirmar';

                    // ...  y Limpiamos la lista de subordinados
                    listaSubordinados.innerHTML = '';

                    // Habilitamos el botón de Agrupados para liberar la categoria
                    btnConfirmar.removeAttribute('disabled');
                }
            });
        });
    </script>

    <!-- Script para manejar la advertencia de cambios no guardados -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let allowNavigation = false;    // para evitar mostrar la advertencia si el usuario efectivamente quiere guardar
            let formChanged = false;        // si hubo modificaciones en los campos del formulario.
            let pendingElement = null;      // elemento sobre el que el usuario hizo click antes de mostrar el modal

            // Si existe catalogos (definido en bloque de Catalogos), captura su estado original
            const catalogosExiste = typeof window.catalogos !== 'undefined';
            //const catalogosOriginal = catalogosExiste ? JSON.stringify(window.catalogos) : null; // Esta línea fue reemplaza por la siguiente debido a que a veces cargaba null 
            const catalogosOriginal = window.catalogosOriginal || null; // ← Agregado por corrección 12-06-25. Guardamos el original el el bloque de Catalogos y lo accedemos desde acá vía 'window'.

            // Si existe relacionesData (definido en bloque de Relaciones de Catalogos), captura su estado original
            const relacionesExiste = typeof window.relacionesData !== 'undefined';
            const relacionesOriginal = relacionesExiste ? structuredClone(window.relacionesData) : [];

             // MARCAR allowNavigation = true AL MOMENTO DE SUBMIT
            document.querySelector('#form').addEventListener('submit', function () {
                allowNavigation = true;
            });

            // Detectar cambios en campos del formulario general
            document.querySelectorAll('#form input, #form select, #form textarea').forEach(el => {
                el.addEventListener('change', () => {
                    formChanged = true;
                });
            });

            function isCatalogosChanged() {
                return catalogosExiste && JSON.stringify(window.catalogos) !== catalogosOriginal;
            }
            function isRelacionesChanged() {
                const limpioOrdenado1 = window.relacionesData.map(r => ({   tipo_relacion_id: r.tipo_relacion_id,
                                                                            valor_origen_id: r.valor_origen_id,
                                                                            valor_destino_id: r.valor_destino_id

                                                                        })).sort((a, b) => {    return (
                                                                                                    a.tipo_relacion_id - b.tipo_relacion_id ||
                                                                                                    a.valor_origen_id - b.valor_origen_id ||
                                                                                                    a.valor_destino_id - b.valor_destino_id
                                                                                                );
                                                                                            });

                const limpioOrdenado2 = relacionesOriginal.map(r => ({  tipo_relacion_id: r.tipo_relacion_id,
                                                                        valor_origen_id: r.valor_origen_id,
                                                                        valor_destino_id: r.valor_destino_id

                                                                    })).sort((a, b) => {    return (
                                                                                                a.tipo_relacion_id - b.tipo_relacion_id ||
                                                                                                a.valor_origen_id - b.valor_origen_id ||
                                                                                                a.valor_destino_id - b.valor_destino_id
                                                                                            );
                                                                                        });

                return JSON.stringify(limpioOrdenado1) !== JSON.stringify(limpioOrdenado2);
            }

            // Advertencia al cerrar la página
            window.addEventListener('beforeunload', function (e) {
                if ((formChanged || isCatalogosChanged() || isRelacionesChanged()) && !allowNavigation) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Intercepta clicks y muestra modal si hay cambios
            document.addEventListener('click', function (e) {
                const el = e.target.closest('a, button');
                if (!el) return;

                // isExcluded = Si corresponde a un elemento que no debe ser vigilado 'no-guard'
                const isExcluded = el.classList.contains('no-guard');
                if ((formChanged || isCatalogosChanged() || isRelacionesChanged())  && !allowNavigation && !isExcluded) {
                    e.preventDefault();
                    pendingElement = el;

                    const modal = new bootstrap.Modal(document.getElementById('modalSalidaNoGuardada'));
                    modal.show();

                    document.getElementById('confirmarSalida').onclick = () => {
                        allowNavigation = true;
                        modal.hide();
                        if (pendingElement.tagName === 'A' && pendingElement.href) {
                            window.location.href = pendingElement.href;
                        } else {
                            pendingElement.click(); // fallback
                        }
                    };
                }
            });
        });
    </script>
@endsection