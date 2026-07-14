<!-- JS para DataTables + Bootstrap 5 -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- JSZip para exportar a Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- pdfmake para exportar a PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Botones de DataTables -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Inicialización de dataTable - Scroll Horizontal y Filtros -->
<script>
    function actualizarScrollHorizontal(contenedorID) {
        const $contenedor = $('#' + contenedorID);
        const $tableScroll = $contenedor.find('.table-scroll');
        const $topScroll = $contenedor.find('.top-scroll');
        const $bottomScroll = $contenedor.find('.bottom-scroll');

        if ($tableScroll.length === 0) return; // previene errores si no existe scroll

        const scrollWidth = $tableScroll[0].scrollWidth;
        const clientWidth = $tableScroll[0].clientWidth;

        if (scrollWidth > clientWidth) {
            $topScroll
                .html('<div style="width:' + scrollWidth + 'px"></div>')
                .removeClass('d-none');
            $bottomScroll
                .html('<div style="width:' + scrollWidth + 'px"></div>')
                .removeClass('d-none');
        } else {
            $topScroll.addClass('d-none').empty();
            $bottomScroll.addClass('d-none').empty();
        }
    }

    function aplicarFiltros(contenedorID) {
        const $contenedor = $('#' + contenedorID);
        const $tabla = $contenedor.find('.listado');
        const $thead = $tabla.find('thead tr:last-child th');

        const filtros = [];

        // Asociar columnas con sus filtros personalizados
        $thead.each(function (index) {
            const textoTh = $(this).text().trim().toLowerCase();

            const $filtro = $contenedor.find('.filtro').filter(function () {
                // return this.id && this.id.toLowerCase() === textoTh;
                return this.id && this.id.toLowerCase().startsWith(textoTh);
            });

            if ($filtro.length) {
                filtros.push({
                    columna: index,
                    $grupo: $filtro
                });
            }
        });

        // Restaurar filtros previos desde localStorage
        const estadoPrevio = JSON.parse(localStorage.getItem('Filtros_' + contenedorID) || '{}');

        for (let grupoID in estadoPrevio) {
            const valores = estadoPrevio[grupoID];
            const $grupo = $contenedor.find('.filtro#' + grupoID);

            if ($grupo.length) {
                $grupo.find('.filtro-toggle').each(function () {
                    const valor = this.value.trim();
                    this.checked = valores.includes(valor);
                });
            }
        }

        // Filtro personalizado de DataTables
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable !== $tabla[0]) return true;

            const row = settings.aoData[dataIndex].nTr;

            for (let filtro of filtros) {
                const valoresSeleccionados = filtro.$grupo.find('.filtro-toggle:checked').map(function () {
                    return this.value.trim();
                }).get();

                const texto = $(row).find('td').eq(filtro.columna).text().trim();

                if (valoresSeleccionados.length === 0 || !valoresSeleccionados.includes(texto)) {
                    return false;
                }
            }

            return true;
        });

        // Escucha cambios en botones toggle
        $contenedor.on('change', '.filtro .filtro-toggle', function () {

            // Guardar filtros al cambiar
            const estadoFiltros = {};

            $contenedor.find('.filtro').each(function () {
                const grupoID = $(this).attr('id');
                const seleccionados = $(this).find('.filtro-toggle:checked').map(function () {
                    return this.value.trim();
                }).get();

                if (grupoID) {
                    estadoFiltros[grupoID] = seleccionados;
                }
            });

            // Guardar en localStorage usando una key única por vista
            localStorage.setItem('Filtros_' + contenedorID, JSON.stringify(estadoFiltros));
    
            // Redibujar tabla
            $tabla.DataTable().draw();
        });
    }

    function inicializarDataTable(contenedorID) {
        // ✅ Invocar función externa para aplicar filtro personalizado
        aplicarFiltros(contenedorID);

        const $contenedor = $('#' + contenedorID);
        const $tabla = $contenedor.find('.listado');

        if (!$tabla.attr('id')) {
            $tabla.attr('id', 'dt_' + contenedorID); // Asigna un ID único basado en el contenedor, si no tiene
        }

        // Verificación para evitar que se re-inicialice si por error se llama dos veces
        if (!$.fn.DataTable.isDataTable($tabla)) {
             $tabla.DataTable({
                autoWidth: true,
                deferRender: true,
                lengthMenu: [
                    //[5, 10, 25, 50, 100, -1],
                    //[5, 10, 25, 50, 100, "Todos"]
                    [5, 10],
                    [5, 10]
                ],
                responsive: true,
                pageLength: 10,
                select: true,

                stateSave: true,
                stateDuration: -1, // guarda indefinidamente
                stateSaveCallback: function (settings, data) {
                    const key = 'DataTables_' + settings.sInstance;
                    localStorage.setItem(key, JSON.stringify(data));
                },
                stateLoadCallback: function (settings) {
                    const key = 'DataTables_' + settings.sInstance;
                    return JSON.parse(localStorage.getItem(key));
                },

                columnDefs: [{
                    orderable: false,
                    targets: ['nosort']
                }],
                language: {
                    url: "/assets/languaje-DataTable/Spanish.json"
                },
                order: [],
                dom: 'lfrtipB', // <-- Agregamos 'f' para que sí se cree el filtro
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Exportar a Excel',
                        className: 'btn btn-success btn-sm mx-2'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm mx-2'
                    }
                ],
                initComplete: function () {
                    const $tableScroll = $contenedor.find('.table-scroll');
                    const $topScroll = $contenedor.find('.top-scroll');
                    const $bottomScroll = $contenedor.find('.bottom-scroll');

                    setTimeout(function () {
                        actualizarScrollHorizontal(contenedorID);

                        $topScroll.on('scroll', function () {
                            $tableScroll.scrollLeft($(this).scrollLeft());
                        });
                        $bottomScroll.on('scroll', function () {
                            $tableScroll.scrollLeft($(this).scrollLeft());
                        });
                        $tableScroll.on('scroll', function () {
                            $topScroll.scrollLeft($(this).scrollLeft());
                            $bottomScroll.scrollLeft($(this).scrollLeft());
                        });
                    }, 100);

                    $tabla.removeClass('d-none');

                    // 🚀 Mover los elementos al lugar correcto
                    const dtWrapper = $tabla.closest('.dataTables_wrapper');

                    // Detectar sufijo dinámico si existe
                    let sufijo = contenedorID.replace(/^contenedor[-_]?/, ''); // 'version-2'
                    sufijo = sufijo.replace(/^version[-_]?/, '');              // '2'
                    sufijo = sufijo ? '-' + sufijo : '';                       // '-2'

                    // Listado de zonas destino y sus clases en DataTables
                    const zonas = [
                        { destino: '#dt-length',     claseDT: '.dataTables_length' },
                        { destino: '#dt-filter',     claseDT: '.dataTables_filter' },
                        { destino: '#dt-info',       claseDT: '.dataTables_info' },
                        { destino: '#dt-paginate',   claseDT: '.dataTables_paginate' },
                        { destino: '#dt-buttons',    claseDT: '.dt-buttons' }
                    ];

                    zonas.forEach(({ destino, claseDT }) => {
                        const selectorV = `${destino}-v${sufijo.replace(/^[-]/, '')}`; // Ej: #dt-length-v2
                        const $destino = $contenedor.find(selectorV).length
                            ? $contenedor.find(selectorV)
                            : $contenedor.find(destino); // fallback retrocompatible

                        $destino.append(dtWrapper.find(claseDT));
                    });

                    $contenedor.find('[id^="dt-length"]').find('label').each(function () {
                        const contenido = $(this).html();
                        const nuevo = contenido
                            .replace('registros', '')
                            .replace('Mostrar', 'Mostrar registros:');
                        $(this).html(nuevo);
                        $(this).css({
                            'display': 'flex',
                            'flex-direction': 'column',
                            'align-items': 'flex-start',
                            'margin-bottom': '0'
                        });
                    });

                    // Fuerza la primera evaluación del filtro personalizado, si existe
                    $tabla.DataTable().draw();

                    // ✅ NUEVO: Reasociar el evento de cambio de cantidad de registros
                    const $lengthSelect = $contenedor.find('#dt-length select');
                    if ($lengthSelect.length) {

                        $lengthSelect.val($tabla.DataTable().page.len()); // 🔥 Sincroniza el valor visual del selector

                        $lengthSelect.off('change').on('change', function () {
                            const newLength = parseInt($(this).val(), 10);
                            $tabla.DataTable().page.len(newLength).draw();
                        });
                    }
                }
            });
        }
    }
</script>
