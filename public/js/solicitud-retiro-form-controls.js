function toggleTipoRetiro($retiro) {

	// 🔒 Si estamos en Región XII, NO aplicamos lógica de Región X
    if (window.regionOperativa === REGION_XII) {
        // En XII:
        // - Tipo siempre BINS
        // - Reposición siempre habilitable

		// Habilitamos sólo si no es EDICION de PLANIFICACION
		if (window.contextoVista?.esEdit && window.contextoVista?.esPlanificacion) {
			// console.log('Es Edición de Planificación -> NO habilitamos grupo reposición ni check requiere reposición.');
		}
		else {
			// console.log('NO es Edición de Planificación -> SI habilitamos grupo reposición ni check requiere reposición.');

			$retiro.find('.grupo-reposicion').removeClass('opacity-50');
			$retiro.find('.requiere_reposicion').prop('disabled', false);
		}

        return;
    }

    // 🔽🔽🔽 Lógica original X Región (SIN CAMBIOS) 🔽🔽🔽

	// Obtenemos elementos Tipo de Retiro, Grupo de Reposición y Requiere
    let $tipoRetiro       = $retiro.find('.tipo_retiro');
    let $tipoRetiroActual = $retiro.find('.tipo_retiro_actual');
    let $grupo            = $retiro.find('.grupo-reposicion');
    let $requiere         = $retiro.find('.requiere_reposicion');

    // Verificamos si estamos en un rebote
	// const index = $retiro.attr('data-index');
	const index = $retiro.closest('.retiro-item-wrapper').data('index');				// Obtenemos index del retiro
	if (typeof index === 'undefined') {
		console.warn('toggleTipoRetiro> index indefinido (se omite).', $retiro);
		return;
	}

    // console.log('index : ', index);

    // Obtenemos el Tipo de Retiro
    let tipoRetiro        = parseInt($tipoRetiro.val());
    let tipoRetiroActual  = parseInt($tipoRetiroActual.val());
    // console.log('parseInt($tipoRetiro.val())       -> tipoRetiro       : ', tipoRetiro);
    // console.log('parseInt($tipoRetiroActual.val()) -> tipoRetiroActual : ', tipoRetiroActual);

    let kilosOld         = 0;
    let tipoRetiroOld    = 0;
	let requiereOld      = 0;
	let cantidadOld      = 0;
	let tipoOperacionOld = 0;
	if (window.esRebote) {
		// console.log('Fué rebote ...');

        kilosOld         = parseInt(window.oldValues.kilogramos_estimados_hidden?.[index]) || 0;   // Si había cantidad para dicho retiro-item lo recuperamos o ponemos 0 (al inicio o retiro nuevo).
        tipoRetiroOld    = parseInt(window.oldValues.tipo_retiro?.[index]) || 0;                   // Si había tipo_retiro para dicho retiro-item lo recuperamos o ponemos 0 (al inicio o retiro nuevo).
		requiereOld      = parseInt(window.oldValues.requiere_reposicion_hidden?.[index]) || 0;    // Si había requiere para dicho retiro-item lo recuperamos o ponemos 0 (al inicio o retiro nuevo).
        cantidadOld      = parseInt(window.oldValues.cantidad_bins_hidden?.[index]) || 0;          // Si había cantidad para dicho retiro-item lo recuperamos o ponemos 0 (al inicio o retiro nuevo).
        tipoOperacionOld = parseInt(window.oldValues.tipo_operacion_hidden?.[index]) || 0;         // Si había tipo_retiro para dicho retiro-item lo recuperamos o ponemos 0 (al inicio o retiro nuevo).
    }
    // console.log('parseInt(window.oldValues.kilogramos_estimados_hidden?.[index]) -> cantidadOld   : ', kilosOld );
    // console.log('parseInt(window.oldValues.tipo_retiro?.[index])                 -> tipoRetiroOld : ', tipoRetiroOld );
    // console.log('parseInt(window.oldValues.requiere_reposicion_hidden?.[index])  -> requiereOld   : ', requiereOld );
    // console.log('parseInt(window.oldValues.cantidad_bins_hidden?.[index])        -> cantidadOld   : ', cantidadOld );
    // console.log('parseInt(window.oldValues.tipo_operacion_hidden?.[index])       -> requiereOld   : ', tipoOperacionOld );

    if (isNaN(tipoRetiro)){                                                // Si el tipo_retiro no posee valor ...
        const fueModificado = $tipoRetiro.data('modificado') === true;     // Vemos si el select2 fue manualmente puesto en 'sin selección'
        // console.log('fueModificado : ', fueModificado);

        if( fueModificado ){ // Si fué manipulado asignamos CERO. Si no, asignamos lo rescatamos de los old.
            tipoRetiro = 0;
        }
        else{ // Verificamos si estamos enRebote
           if (window.esRebote){ // Si estamos enRebote existe OLD
               tipoRetiro = tipoRetiroOld;                    // Asignamos lo que se rescata desde el old.
           }
           else{
               tipoRetiro = tipoRetiroActual;                 // Asignamos el 'actual' que al principio también es cero.
           }
        }
    }
    // console.log('tipoRetiro : ', tipoRetiro);

    const editandoPlanificación = contextoVista.esEdit && contextoVista.esPlanificacion;
    if ( ! editandoPlanificación ) {                                                // Si no estamos EDITANDO de PLANIFICACION ajustamos según Tipo de Retiro
        // console.log('toggleTipoRetiro> NO ESTA EDITANDO PLANIFICACION ...');

        if (tipoRetiro === TIPO_RETIRO_BINS) {
            $grupo.removeClass('opacity-50');
            $requiere.prop('disabled', false);

            if (window.esRebote) {                                                  // Si estamos en rebote ...
                $requiere.prop('checked', parseInt(requiereOld) === 1 ? 1 : 0);     // Dejamos el checkbox según lo que dice lo recuperado en el arreglo de Old
            }

        } else { // No es tipo BINS (incluye TOLVA, NaN - vacío, futuro tipo) → se limpia todo
            $grupo.addClass('opacity-50');
            $requiere.prop('disabled', true).prop('checked', false);
        }

        $requiere.trigger('change');                                                // Activamos el evento Change del elemento requiere reposición

    }                                                                               // De lo contrario no hacemos nada y se queda tal como se armó el blade
    else{
        // console.log('toggleTipoRetiro> SI ESTA EDITANDO PLANIFICACION ...');
    }
}
$(document).on('change', '.tipo_retiro', function () {
    $(this).data('modificado', true); // Marcamos como modificado el atributo
    toggleTipoRetiro($(this).closest('.retiro-region'));
});


// 🔹 Función de validación de coherencia entre RegiónOperativa, Kilos y Tipo de Operacón. Se utiliza SOLO en JS de SUBMIT.
function validarBinesRequiereReposicion($bloque) {
	const requiereReposicion = $bloque.find('.requiere_reposicion')[0];
	const conReposicion      = requiereReposicion?.checked || false;

	const cantidadBines 	 = $bloque.find('.cantidad_bins')[0];
	const cantBines          = parseInt(cantidadBines?.value || 0, 10);

	// Limpiar errores previos
	[cantidadBines, requiereReposicion].forEach(el => {
		if (el) el.setCustomValidity('');
	});

	// Reglas
	let primerError = null;

	// console.log('Requiere Reposicion : ', conReposicion);
	// console.log('Cantidad de Bines   : ', cantBines);

	if (cantBines <= 0 && conReposicion) {
		cantidadBines.setCustomValidity('Si requiere reposición de bines la cantidad a reponer debe ser mayor a 0.');
		if (!primerError) primerError = cantidadBines;
	}

	return primerError;
}

$(document).on('input change', '.cantidad_bins', function () {
	this.setCustomValidity('');													// Limpiamos mensajes de error de validaciones previas que pudieran estar activos
});

function toggleCantidadBins(context) {

    // 🔒 Región XII: reglas propias (SOLO UI)
    if (window.regionOperativa === REGION_XII) {

		// Tocamos cantidad de bins SÓLO si NO es EDICION de PLANIFICACION
		if (window.contextoVista?.esEdit && window.contextoVista?.esPlanificacion) {
			// console.log('Es Edición de Planificación -> NO tocamos cantiad de bins a reponer.');
		}
		else {
			const $checkbox = context.find('.requiere_reposicion');
			const $cantidad = context.find('.cantidad_bins');

			// console.log('NO es Edición de Planificación -> SI tocamos cantiad de bins a reponer.');

			if ($checkbox.is(':checked')) {
				$cantidad.prop('disabled', false);
			} else {
				$cantidad.prop('disabled', true).val('0');
			}
		}

		return;
    }

    // 🔽🔽🔽 Luego viene TODA la lógica original X Región (sin cambios) 🔽🔽🔽

    // Obtenemos elementos Tipo de Retiro, CheckBox, Cantidad_Bins
    let $tipoRetiro = context.find('.tipo_retiro');
    let $tipoRetiroActual = context.find('.tipo_retiro_actual');

    let $checkbox = context.find('.requiere_reposicion');
    let $cantidad = context.find('.cantidad_bins');

    // Verificamos si estamos en un rebote
	// const index = context.attr('data-index');                                                // Obtenemos index del retiro
	const index = context.closest('.retiro-item-wrapper').data('index');
	if (typeof index === 'undefined') {
		console.warn('toggleCantidadBins> index indefinido (se omite).', context);
		return;
	}

    // console.log('index : ', index);

    // Obtenemos el Tipo de Retiro
    let tipoRetiro        = parseInt($tipoRetiro.val());
    let tipoRetiroActual  = parseInt($tipoRetiroActual.val());
    let cantidad          = parseInt($cantidad.val());
    // console.log('parseInt($tipoRetiro.val())       -> tipoRetiro       : ', tipoRetiro);
    // console.log('parseInt($tipoRetiroActual.val()) -> tipoRetiroActual : ', tipoRetiroActual);
    // console.log('parseInt($cantidad.val())         -> cantidad         : ', cantidad);

    let tipoRetiroOld = 0;
    let cantidadOld   = 0;
    if (window.esRebote){                                                                       // Y si estamos en Rebote calculamos:
        tipoRetiroOld = parseInt(window.oldValues.tipo_retiro?.[index]) || 0;                   // Si había tipo_retiro para dicho retiro-item lo recuperamos o ponemos 0 (al inicio o retiro nuevo).
        cantidadOld   = parseInt(window.oldValues.cantidad_bins_hidden?.[index]) || 0;          // Si había cantidad para dicho retiro-item lo recuperamos o ponemos 0 (al inicio o retiro nuevo).
    }
    // console.log('parseInt(window.oldValues.tipo_retiro?.[index])          -> tipoRetiroOld : ', tipoRetiroOld);
    // console.log('parseInt(window.oldValues.cantidad_bins_hidden?.[index]) -> cantidadOld   : ', cantidadOld);

    if (isNaN(tipoRetiro)){                                                // Si el tipo_retiro no posee valor ...
        const fueModificado = $tipoRetiro.data('modificado') === true;     // Vemos si el select2 fue manualmente puesto en 'sin selección'
        // console.log('fueModificado : ', fueModificado);

        if( fueModificado ){ // Si fué manipulado asignamos CERO. Si no, asignamos lo rescatamos de los old.
            tipoRetiro = 0;
        }
        else{ // Verificamos si estamos enRebote
           if (window.esRebote){ // Si estamos enRebote existe OLD
                tipoRetiro = tipoRetiroOld;                    // Asignamos el tipo_retiro que rescatamos desde el old.
                cantidad = cantidadOld;                        // Obtenemos la cantidad que rescatamos de los old (si no había viene 0).
           }
           else{
                tipoRetiro = tipoRetiroActual;                 // Asignamos el 'actual' que al principio también es cero.
           }
        }
    }
    // console.log('tipoRetiro : ', tipoRetiro);
    // console.log('cantidad   : ', cantidad);

    const editandoPlanificación = contextoVista.esEdit && contextoVista.esPlanificacion;
    if ( ! editandoPlanificación ) {                                            // Si no estamos EDITANDO de PLANIFICACION ajustamos según Tipo de Retiro
        // console.log('toggleCantidadBins> NO ESTA EDITANDO PLANIFICACION ...');

        if (tipoRetiro === TIPO_RETIRO_BINS) {                                  // ES tipo de retiro BINs
            if (!$checkbox.prop('disabled') && $checkbox.is(':checked')) {      // Si está ENABLED y CHECKEADO
                $cantidad.prop('disabled', false).val(cantidad);                // Activamos con la CANTIDAD que corresponde: del rebote y si no había o no estamos en rebote : CERO
            } else {
                $cantidad.prop('disabled', true).val('0');                      // Desactivamos con un 0
            }

        } else {                                                                // No es tipo BINS (incluye TOLVA, NaN - vacío, futuro tipo) → se limpia todo
            $cantidad.prop('disabled', true).val('');                           // Desactivamos con un '' (vacío)
        }

    }                                                                           // De lo contrario no hacemos nada y se queda tal como se armó el blade
    else{
        // console.log('toggleCantidadBins> SI ESTA EDITANDO PLANIFICACION ...');
    }

}
$(document).on('change', '.requiere_reposicion', function () {					// Los eventos deben ejecutar lógica SOLO sobre el contexto donde ocurrieron.
	toggleCantidadBins($(this).closest('.retiro-region'));						// Por eso cambiamos .closest('.retiro-item') por .closest('.retiro-region')
});

// 🔹 Función de validación de coherencia entre RegiónOperativa, Kilos y Tipo de Operacón. Se utiliza SOLO en JS de SUBMIT.
function validarKilosTipoOperacion($bloque) {
	const kilos = $bloque.find('.kilogramos_estimados')[0];

    // Reglas
	let primerError = null;

	// 1. Región X → kilos no pueden ser <= 0
	if (window.regionOperativa === REGION_X) {
		// Limpiar errores previos
		[kilos].forEach(el => {
			if (el) el.setCustomValidity('');
		});

		if (kilos.value <= 0) {
			kilos.setCustomValidity('En Región X Kilos Estimados debe ser mayor a 0.');
			if (!primerError) primerError = kilos;
		}
	}

	// 2. Región XII → si kilos = 0 → debe ser reposición
	if (window.regionOperativa === REGION_XII) {
		const tipoOperacion = $bloque.find('.tipo_operacion')[0];
		const esReposicion = tipoOperacion.checked;

		// Limpiar errores previos
		[kilos, tipoOperacion].forEach(el => {
			if (el) el.setCustomValidity('');
		});

		if (kilos.value <= 0 && !esReposicion) {
			tipoOperacion.setCustomValidity('En Región XII si Kilos Estimados es 0 Tipo de Operación debe ser Reposición.');
			if (!primerError) primerError = tipoOperacion;
		}
	}

	return primerError;
}

function toggleKilosEstimados($retiro) {
    const $kilogramosEstimados = $retiro.find('.kilogramos_estimados');
	const $hiddenKilosEstimados = $retiro.find('.kilogramos_estimados_hidden');

	const $tipoOperacion       = $retiro.find('.tipo_operacion');
	const $hiddenTipoOperacion = $retiro.find('.tipo_operacion_hidden');

    const kilosEstimados = $kilogramosEstimados[0];
    const tipoOperacion = $tipoOperacion[0];

    // 🧼 LIMPIEZA
    [kilosEstimados, tipoOperacion].forEach(el => {
        if (el) el.setCustomValidity('');
    });

    // Tomamos valor numérico seguro
    let kilos = parseFloat($kilogramosEstimados.val());
	$hiddenKilosEstimados.val(kilos);							// Aprovechamos de replicar el input de Kilos en su respectivo Hidden

    if (isNaN(kilos)) kilos = 0;

    if (kilos > 0) {
		// 🔴 Hay kilos → NO puede cambiar tipo operación
        $tipoOperacion.prop('checked', false);   // opcional: lo reseteas
		$tipoOperacion.prop('disabled', true);

    } else {
		// 🟢 No hay kilos → puede elegir tipo operación
        $tipoOperacion.prop('disabled', false);
	}

	const valorCheck = $tipoOperacion.prop('checked') ? '1' : '0';
	$hiddenTipoOperacion.val(valorCheck);

	// console.log('toggleKilosEstimados> tipoOperacion VISIBLE :', $tipoOperacion.prop('checked'));
	// console.log('toggleKilosEstimados> tipoOperacion HIDDEN  :', $hiddenTipoOperacion.val());
}
$(document).on('change', '.kilogramos_estimados', function () {
	toggleKilosEstimados($(this).closest('.retiro-region'));
});

function toggleTipoOperacion($retiro) {
	const $kilogramosEstimados  = $retiro.find('.kilogramos_estimados');
	const $hiddenKilosEstimados = $retiro.find('.kilogramos_estimados_hidden');
	const $tipoOperacion        = $retiro.find('.tipo_operacion');
	const $hiddenTipoOperacion  = $retiro.find('.tipo_operacion_hidden');

	const $checkRequiere        = $retiro.find('.requiere_reposicion');

    const kilosEstimados        = $kilogramosEstimados[0];
    const tipoOperacion         = $tipoOperacion[0];

    // 🧼 LIMPIEZA
    [kilosEstimados, tipoOperacion].forEach(el => {
        if (el) el.setCustomValidity('');
    });

	const esReposicion = $tipoOperacion.prop('checked');
	if (esReposicion) {
		// 🔴 Es reposición → NO se permiten kilos
		$kilogramosEstimados.val(0);        // opcional pero recomendable para consistencia
		$kilogramosEstimados.prop('disabled', true);

		const kilos = parseFloat($kilogramosEstimados.val());
		$hiddenKilosEstimados.val(kilos);							// Aprovechamos de replicar el input de Kilos en su respectivo Hidden

		if (kilosEstimados) kilosEstimados.setCustomValidity(''); // limpia explícitamente eventual mensaje de error previo

		if (window.esRebote) {                                                      // Si estamos en rebote ...
			$checkRequiere.prop('checked', parseInt(requiereOld) === 1 ? 1 : 0);    // Dejamos el checkbox según lo que dice lo recuperado en el arreglo de Old
		}
		else {
			$checkRequiere.prop('checked', true);									// Si no es rebote se activa switch dado que Tipo Operación quedó en REPOSICION
		}

	} else {
		// 🟢 Es retiro → se permiten kilos
		$kilogramosEstimados.prop('disabled', false);
	}

	const valorCheck = $tipoOperacion.prop('checked') ? '1' : '0';
	$hiddenTipoOperacion.val(valorCheck);

	// console.log('toggleTipoOperacion> tipoOperacion VISIBLE :', $tipoOperacion.prop('checked'));
	// console.log('toggleTipoOperacion> tipoOperacion HIDDEN  :', $hiddenTipoOperacion.val());

	// Cada vez que cambie el SWITCH de Tipo Operación se debe repintar la vista
	// Para ocultar o hacer aparecer elementos de la porción de PLANIFICACION
	aplicarRegionOperativa(window.regionOperativa);

}
$(document).on('change', '.tipo_operacion', function () {						// Los eventos deben ejecutar lógica SOLO sobre el contexto donde ocurrieron.
	toggleTipoOperacion($(this).closest('.retiro-region'));
});

function inicializarRetiroContexto($retiro) {
    const selectTipoRetiro = $retiro.find('.tipo_retiro');
    const hiddenTipoRetiro = $retiro.find('.tipo_retiro_actual');

	// 🔹 Precarga Select2 Tipo Retiro, usamos catalogo2select2Element para inmunizarnos de que en ambos contextos se repiten los IDs y siempre saca el primero (Región X)
    catalogo2select2Element( CATEGORIA_TIPO_RETIRO, selectTipoRetiro, 'Seleccione tipo de retiro', hiddenTipoRetiro );

    // 🔹 Aplicar reglas de negocio según tipo de retiro
    toggleTipoRetiro($retiro);
}

// Inicializar Select2 de tipo de retiro para cada retiro-item
// Esta funcion se invoca desde retiro-interacciones-plantas-productoras.js : $('#sucursal_retiro').on('change', function ()
// Aca en este fuente lo usamos en el listener de DOM Content Loaded (al final de la carga - para que los OLDs ya estén leidos)
function inicializarSolicitudRetiro() {
	// Activación de contexto regional para hacer visible retiro(s)
	aplicarRegionOperativa(window.regionOperativa);

	// Inicializar lógicas específicas de los retiros presentados
	let retiroContexto = null;
	switch (window.regionOperativa) {
		case REGION_X:
			retiroContexto = '.retiro-region-x';
			break;

		case REGION_XII:
			retiroContexto = '.retiro-region-xii';
			break;

		default:	// Si no es X o XII deja ambas ocultas
			console.warn('DOMContentLoaded> region operativa indefinida (se omite) : ', window.regionOperativa);
			return;
	}
	$(retiroContexto).filter(function () {											// Buscamos todos los retiro-contexto regional
		return $(this).closest('.retiro-item-template').length === 0;				// pero omitimos el del Template
	}).each(function () {
		inicializarRetiroContexto($(this));
	});
}
document.addEventListener('DOMContentLoaded', inicializarSolicitudRetiro);
