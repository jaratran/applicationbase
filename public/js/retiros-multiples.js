// Reindexación de campos de Solicitud de Retiro
function reindexarCamposSolicitud($elemento, index) {
    $elemento.find(`label[for^="fecha_retiro_"]`).attr('for', 'fecha_retiro_' + index);
    $elemento.find('.fecha_retiro')
        .attr('id',   'fecha_retiro_' + index)
        .attr('name', 'fecha_retiro[' + index + ']');

    $elemento.find(`label[for^="tipo_retiro_"]`).attr('for', 'tipo_retiro_' + index);
    $elemento.find('.tipo_retiro')
        .attr('id',   'tipo_retiro_' + index)
        .attr('name', 'tipo_retiro[' + index + ']');

        $elemento.find('.tipo_retiro_actual') // Hidden, sólo actualiza el id.
            .attr('id',   'tipo_retiro_actual_' + index)

        $elemento.find('.tipo_retiro_original') // Hidden, sólo actualiza el id.
            .attr('id',   'tipo_retiro_original_' + index)

    $elemento.find(`label[for^="kilogramos_estimados_"]`).attr('for', 'kilogramos_estimados_' + index);
    $elemento.find('.kilogramos_estimados')
        .attr('id',   'kilogramos_estimados_' + index)
        .attr('name', 'kilogramos_estimados[' + index + ']');

		$elemento.find('.kilogramos_estimados_hidden') // Hidden, sólo actualiza el id.
		.attr('id',   'kilogramos_estimados_hidden_' + index);

    $elemento.find(`label[for^="requiere_reposicion_"]`).attr('for', 'requiere_reposicion_' + index);
    $elemento.find('.requiere_reposicion')
        .attr('id',   'requiere_reposicion_' + index)
        .attr('name', 'requiere_reposicion[' + index + ']');

        $elemento.find('.requiere_reposicion_hidden') // Hidden, sólo actualiza el id.
            .attr('id',   'requiere_reposicion_hidden_' + index);

    $elemento.find(`label[for^="cantidad_bins_"]`).attr('for', 'cantidad_bins_' + index);
    $elemento.find('.cantidad_bins')
        .attr('id',   'cantidad_bins_' + index)
        .attr('name', 'cantidad_bins[' + index + ']');

        $elemento.find('.cantidad_bins_hidden') // Hidden, sólo actualiza el id.
            .attr('id',   'cantidad_bins_hidden_' + index);

    $elemento.find(`label[for^="tipo_operacion_"]`).attr('for', 'tipo_operacion_' + index);
    $elemento.find('.tipo_operacion')
        .attr('id',   'tipo_operacion_' + index)
        .attr('name', 'tipo_operacion[' + index + ']');

        $elemento.find('.tipo_operacion_hidden') // Hidden, sólo actualiza el id.
            .attr('id',   'tipo_operacion_hidden_' + index);
}

// Reindexación de campos de Planificación del Retiro
function reindexarCamposPlanificacion($elemento, index) {
    $elemento.find('label[for^="fecha_planificada_"]').attr('for', 'fecha_planificada_' + index);
    $elemento.find('.fecha_planificada')
        .attr('id',   'fecha_planificada_' + index)
        .attr('name', 'fecha_planificada[' + index + ']');

    $elemento.find('label[for^="duracion_viaje_"]').attr('for', 'duracion_viaje_' + index);
    $elemento.find('.duracion_viaje')
        .attr('id',   'duracion_viaje_' + index)
        .attr('name', 'duracion_viaje[' + index + ']');

	$elemento.find('label[for^="duracion_estimada_dias_"]').attr('for', 'duracion_estimada_dias_' + index);
    $elemento.find('.duracion_estimada_dias')
        .attr('id',   'duracion_estimada_dias_' + index)
        .attr('name', 'duracion_estimada_dias[' + index + ']');

    $elemento.find('label[for^="hora_llegada_"]').attr('for', 'hora_llegada_' + index);
    $elemento.find('.hora_llegada')
        .attr('id',   'hora_llegada_' + index)
        .attr('name', 'hora_llegada[' + index + ']');

        $elemento.find('.hora_llegada_estimada_hidden') // Hidden, solo reindexa ID
            .attr('id',   'hora_llegada_estimada_hidden_' + index);

	$elemento.find('label[for^="eta_calculada_"]').attr('for', 'eta_calculada_' + index);
    $elemento.find('.eta_calculada')
        .attr('id',   'eta_calculada_' + index)
        .attr('name', 'eta_calculada[' + index + ']');

        $elemento.find('.eta_calculada_hidden') // Hidden, solo reindexa ID
            .attr('id',   'eta_calculada_hidden_' + index);

	$elemento.find('label[for^="tipo_materia_prima_"]').attr('for', 'tipo_materia_prima_' + index);
    $elemento.find('.tipo_materia_prima')
        .attr('id',   'tipo_materia_prima_' + index)
        .attr('name', 'tipo_materia_prima[' + index + ']');

        $elemento.find('.tipo_materia_prima_actual') // Hidden, solo reindexa ID
            .attr('id', 'tipo_materia_prima_actual_' + index);

        $elemento.find('.tipo_materia_prima_original') // Hidden, solo reindexa ID
            .attr('id', 'tipo_materia_prima_original_' + index);

    $elemento.find('label[for^="especie_"]').attr('for', 'especie_' + index);
    $elemento.find('.especie')
        .attr('id',   'especie_' + index)
        .attr('name', 'especie[' + index + ']');

        $elemento.find('.especie_actual') // Hidden, solo reindexa ID
            .attr('id', 'especie_actual_' + index);

        $elemento.find('.especie_original') // Hidden, solo reindexa ID
            .attr('id', 'especie_original_' + index);

    $elemento.find('label[for^="tiene_restriccion_"]').attr('for', 'tiene_restriccion_' + index);
    $elemento.find('.tiene_restriccion')
        .attr('id',   'tiene_restriccion_' + index)
        .attr('name', 'tiene_restriccion[' + index + ']');

        $elemento.find('.tiene_restriccion_hidden') // Hidden, solo reindexa ID
            .attr('id', 'tiene_restriccion_hidden_' + index);

	$elemento.find('label[for^="tipo_transporte_"]').attr('for', 'tipo_transporte_' + index);
    $elemento.find('.tipo_transporte')
        .attr('id',   'tipo_transporte_' + index)
        .attr('name', 'tipo_transporte[' + index + ']');

        $elemento.find('.tipo_transporte_actual') // Hidden, solo reindexa ID
            .attr('id', 'tipo_transporte_actual_' + index);

        $elemento.find('.tipo_transporte_original') // Hidden, solo reindexa ID
            .attr('id', 'tipo_transporte_original_' + index);

	$elemento.find('label[for^="fecha_embarque_"]').attr('for', 'fecha_embarque_' + index);
    $elemento.find('.fecha_embarque')
        .attr('id',   'fecha_embarque_' + index)
        .attr('name', 'fecha_embarque[' + index + ']');

        $elemento.find('.fecha_embarque_hidden') // Hidden, solo reindexa ID
            .attr('id',   'fecha_embarque_hidden_' + index);

	$elemento.find('label[for^="fecha_arribo_puerto_"]').attr('for', 'fecha_arribo_puerto_' + index);
    $elemento.find('.fecha_arribo_puerto')
        .attr('id',   'fecha_arribo_puerto_' + index)
        .attr('name', 'fecha_arribo_puerto[' + index + ']');

        $elemento.find('.fecha_arribo_puerto_hidden') // Hidden, solo reindexa ID
            .attr('id',   'fecha_arribo_puerto_hidden_' + index);

    $elemento.find('label[for^="patente_rampla_"]').attr('for', 'patente_rampla_' + index);
    $elemento.find('.patente_rampla')
        .attr('id',   'patente_rampla_' + index)
        .attr('name', 'patente_rampla[' + index + ']');

		$elemento.find('.patente_rampla_actual') // Hidden, solo reindexa ID
            .attr('id', 'patente_rampla_actual_' + index);

		$elemento.find('.patente_rampla_texto') // Hidden, solo reindexa ID
            .attr('id', 'patente_rampla_texto_' + index);

        $elemento.find('.patente_rampla_original') // Hidden, solo reindexa ID
            .attr('id', 'patente_rampla_original_' + index);

    $elemento.find('label[for^="estado_rampla_"]').attr('for', 'estado_rampla_' + index);
    $elemento.find('.estado_rampla')
        .attr('id',   'estado_rampla_' + index)
        .attr('name', 'estado_rampla[' + index + ']');

		$elemento.find('.estado_rampla_actual') // Hidden, solo reindexa ID
            .attr('id', 'estado_rampla_actual_' + index);

        $elemento.find('.estado_rampla_original') // Hidden, solo reindexa ID
            .attr('id', 'estado_rampla_original_' + index);

    $elemento.find('label[for^="patente_camion_"]').attr('for', 'patente_camion_' + index);
    $elemento.find('.patente_camion')
        .attr('id',   'patente_camion_' + index)
        .attr('name', 'patente_camion[' + index + ']');

        $elemento.find('.patente_camion_actual') // Hidden, solo reindexa ID
            .attr('id', 'patente_camion_actual_' + index);

        $elemento.find('.patente_camion_texto') // Hidden, solo reindexa ID
            .attr('id', 'patente_camion_texto_' + index);

        $elemento.find('.patente_camion_original') // Hidden, solo reindexa ID
            .attr('id', 'patente_camion_original_' + index);

    $elemento.find('label[for^="transportista_"]').attr('for', 'transportista_' + index);
    $elemento.find('.transportista')
        .attr('id',   'transportista_' + index)
        .attr('name', 'transportista[' + index + ']');

        $elemento.find('.transportista_id') // Hidden, solo reindexa ID
            .attr('id', 'transportista_id_' + index);

    $elemento.find('label[for^="tipo_camion_"]').attr('for', 'tipo_camion_' + index);
    $elemento.find('.tipo_camion')
        .attr('id',   'tipo_camion_' + index)
        .attr('name', 'tipo_camion[' + index + ']');

    $elemento.find('label[for^="conductor_"]').attr('for', 'conductor_' + index);
    $elemento.find('.conductor')
        .attr('id',   'conductor_' + index)
        .attr('name', 'conductor[' + index + ']');

        $elemento.find('.conductor_actual') // Hidden, solo reindexa ID
            .attr('id', 'conductor_actual_' + index);

        $elemento.find('.conductor_texto') // Hidden, solo reindexa ID
            .attr('id', 'conductor_texto_' + index);

        $elemento.find('.conductor_original') // Hidden, solo reindexa ID
            .attr('id', 'conductor_original_' + index);

	$elemento.find('label[for^="fecha_rescate_puerto_"]').attr('for', 'fecha_rescate_puerto_' + index);
    $elemento.find('.fecha_rescate_puerto')
        .attr('id',   'fecha_rescate_puerto_' + index)
        .attr('name', 'fecha_rescate_puerto[' + index + ']');

        $elemento.find('.fecha_rescate_puerto_hidden') // Hidden, solo reindexa ID
            .attr('id',   'fecha_rescate_puerto_hidden_' + index);

    $elemento.find('label[for^="camion_rescate_"]').attr('for', 'camion_rescate_' + index);
    $elemento.find('.camion_rescate')
        .attr('id',   'camion_rescate_' + index)
        .attr('name', 'camion_rescate[' + index + ']');

        $elemento.find('.camion_rescate_actual') // Hidden, solo reindexa ID
            .attr('id', 'camion_rescate_actual_' + index);

        $elemento.find('.camion_rescate_texto') // Hidden, solo reindexa ID
            .attr('id', 'camion_rescate_texto_' + index);

        $elemento.find('.camion_rescate_original') // Hidden, solo reindexa ID
            .attr('id', 'camion_rescate_original_' + index);

    $elemento.find('label[for^="conductor_rescate_"]').attr('for', 'conductor_rescate_' + index);
    $elemento.find('.conductor_rescate')
        .attr('id',   'conductor_rescate_' + index)
        .attr('name', 'conductor_rescate[' + index + ']');

        $elemento.find('.conductor_rescate_actual') // Hidden, solo reindexa ID
            .attr('id', 'conductor_rescate_actual_' + index);

        $elemento.find('.conductor_rescate_texto') // Hidden, solo reindexa ID
            .attr('id', 'conductor_rescate_texto_' + index);

        $elemento.find('.conductor_rescate_original') // Hidden, solo reindexa ID
            .attr('id', 'conductor_rescate_original_' + index);
}

// Actualizar indices de retiros producto de agregar o eliminar retiros
function actualizarIndicesRetiros() {
    const $retiros = $('.retiro-item');
    const $botonesEliminar = $('.btnRemoveRetiro');

    if ($retiros.length <= 1) {
        $botonesEliminar.addClass('d-none');
    } else {
        $botonesEliminar.removeClass('d-none');
    }

    /*
        ╔═══════════════════════════════════════════════════════════════════╗
        ║ REGLAS PARA REINDEXACIÓN Y USO DE CAMPOS DINÁMICOS EN FORMULARIOS ║
        ║ ------------------------------------------------------------------║
        ║ - Todo campo dinámico debe poseer y reindexar su ID               ║
        ║ - Solo campos visibles deben reindexar también su NAME            ║
        ║ - Si es solo para frontend (JS)   → NO requiere name[]            ║
        ║ - Si el campo se envía al backend → Debe tener name[]             ║
        ╚═══════════════════════════════════════════════════════════════════╝
    */
    $('.retiro-item').each(function (index) {
        let numero = index+1; // Que el rotulo del retiro comience desde 1 pero los elementos comienzan desde 0
        $(this).find('.titulo-retiro').text('Datos del Retiro #' + numero);

        // Actualizamos el index del bloque retiro-item
        $(this).attr('data-index', index);

        // Actualizamos el index del bloque interno llamado retiro-item-wrapper
        $(this).find('.retiro-item-wrapper').attr('data-index', index);

        // Re-indexación del Detalle del Retiro
        reindexarCamposSolicitud($(this), index);

        // Re-indexación del Detalle de la Planificación
        const tienePlanificacion = $(this).find('.detalle-planificacion').length > 0;
        if( tienePlanificacion ){
            reindexarCamposPlanificacion($(this), index);
        }
    });
}

// Agregar retiros
$('#btnAddRetiro').off('click').on('click', function () {
    let nuevoIndex = $('.retiro-item').length;

    // Clonamos un retiro-itme-template el cual viene con todos los elementos pre-configurados, pre-cargados y limpios
    let $cloned = $('.retiro-item-template')
                    .clone()
                    .removeClass('retiro-item-template d-none')
                    .addClass('retiro-item')
                    .attr('data-index', nuevoIndex);

	// Actualizamos el index del bloque interno llamado retiro-item-wrapper
	$cloned.find('.retiro-item-wrapper').attr('data-index', nuevoIndex);

    // Agregar el clon al contenedor
    $('#retiros-container').append($cloned);

	// Inicializar hidden obligatorios en el nuevo retiro (CRÍTICO)
	$cloned.find('input[name="kilogramos_estimados[]"]').val('0').attr('data-original', '0');
	$cloned.find('input[name="kilogramos_estimados_hidden[]"]').val('');

	$cloned.find('input[name="requiere_reposicion_hidden[]"]').val('0');
	$cloned.find('input[name="cantidad_bins_hidden[]"]').val('');
	$cloned.find('input[name="tipo_operacion_hidden[]"]').val('0');

	$cloned.find('input[name="tiene_restriccion_hidden[]"]').val('0');

    // // ✅ Inicializar Select2 correctamente
    $cloned.find('.select2')
                    .removeClass('select2-hidden-accessible')
                    .removeAttr('data-select2-id aria-hidden')
                    .next('.select2').remove().end()
                    .select2({ theme: 'bootstrap' });

	// ✅ Inicializar Flatpickr SOLO en los nuevos .timepicker dentro del clon
    $cloned.find('.timepicker').each(function () {
        if (!this._flatpickr) { // evita re-inicializar los existentes
            flatpickr(this, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
                disableMobile: true
            });
        }
    });

    // Actualizar visibilidad del botón "Eliminar Retiro" y reindexar si aplica
    actualizarIndicesRetiros();

    // Agregamos un casillero nuevo al final del arreglo de OLDs a fin de mantener coherencia con los indices
    agregarOldRetiro();

	// Repasar activación de contexto regional para hacer visible el nuevo elemento
	aplicarRegionOperativa(window.regionOperativa);

	// Inicializar lógica específica del retiro recién clonado.
	let retiroContexto = null;
	switch (window.regionOperativa) {
		case REGION_X:
			retiroContexto = '.retiro-region-x';
			break;

		case REGION_XII:
			retiroContexto = '.retiro-region-xii';
			break;

		default:	// Si no es X o XII deja ambas ocultas
			console.warn('btnAddRetiro> region operativa indefinida (se omite) : ', window.regionOperativa);
			return;
	}
	inicializarRetiroContexto($cloned.find(retiroContexto));
});

// Eliminar retiros
let $retiroAEliminar = null;
$(document).on('click', '.btnRemoveRetiro', function () {
    if ($('.retiro-item').length <= 1) {
        alert("Debe haber al menos un retiro.");
        return;
    }

    $retiroAEliminar = $(this).closest('.retiro-item');
    $('#confirmDeleteRetiroModal').modal('show');
});

// Confirmar eliminación de retiro desde la Modal
$('#btnConfirmDeleteRetiro').on('click', function (e) {
    e.preventDefault(); // Evita cualquier comportamiento por defecto, aunque no sea crítico aquí

    if ($retiroAEliminar && $retiroAEliminar.length) {
        // Primero eliminamos el respectivo casillero (index) del arreglo de OLDs a fin de mantener coherencia con los indices
        const index    = $retiroAEliminar.attr('data-index');    // Obtenemos index
        eliminarOldRetiro(index);                                // Eliminamos el correspondiente casillero en los arreglos de OLDs que guardan rebote

        $retiroAEliminar.remove();                               // Después podemos removerlo del DOM
        actualizarIndicesRetiros();
        $retiroAEliminar = null;
    }

    $('#confirmDeleteRetiroModal').modal('hide');
});
