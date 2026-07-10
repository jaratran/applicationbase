// Sincronización de Hidden antes del Submit con Control para evitar doble Submit
$('#formSolicitud').on('submit', function (e) {

	// 🔁 Controlar doble submit
    // Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
    if (this.enviado) {
        console.log("⛔ Doble submit detectado, se detiene.");
        e.preventDefault();
        return;
    }

	// 🔎 Validación de kilos vs tipo de operación según región operativa. Se apoya con función de validación existente en solicitud-retiro-form-control.js
	{
		let hayErrores = false;
		let primerErrorGlobal = null;
		let selectorRegion = null;

		switch (window.regionOperativa) {
			case REGION_X:
				selectorRegion = '.retiro-region-x';
				break;

			case REGION_XII:
				selectorRegion = '.retiro-region-xii';
				break;

			default:
				console.warn('⚠️ Región operativa no soportada:', window.regionOperativa);
				return;
		}

		$(selectorRegion).filter(function () {										// Buscamos todos los selectorRegion del contexto regional activo
			return $(this).closest('.retiro-item-template').length === 0;			// pero omitimos el del Template
		}).each(function () {
			const error = validarKilosTipoOperacion($(this));						// Validamos usando función definida en solicitud-retiro-form-control.js
			if (error) {
				hayErrores = true;
				if (!primerErrorGlobal) primerErrorGlobal = error;
			}
		});

		if (hayErrores) {
			e.preventDefault();

			if (primerErrorGlobal) {
				primerErrorGlobal.reportValidity();
				primerErrorGlobal.focus();
			}

			return; // ⛔ Detener submit
		}
	}


	// 🔎 Validación de Cantidad de Bines vs Requerie Reposición. Se apoya con función de validación existente en solicitud-retiro-form-control.js
	{
		let hayErrores = false;
		let primerErrorGlobal = null;
		let selectorRegion = null;

		switch (window.regionOperativa) {
			case REGION_X:
				selectorRegion = '.retiro-region-x';
				break;

			case REGION_XII:
				selectorRegion = '.retiro-region-xii';
				break;

			default:
				console.warn('⚠️ Región operativa no soportada:', window.regionOperativa);
				return;
		}

		$(selectorRegion).filter(function () {										// Buscamos todos los selectorRegion del contexto regional activo
			return $(this).closest('.retiro-item-template').length === 0;			// pero omitimos el del Template
		}).each(function () {
			const error = validarBinesRequiereReposicion($(this));					// Validamos usando función definida en solicitud-retiro-form-control.js
			if (error) {
				hayErrores = true;
				if (!primerErrorGlobal) primerErrorGlobal = error;
			}
		});

		if (hayErrores) {
			e.preventDefault();

			if (primerErrorGlobal) {
				primerErrorGlobal.reportValidity();
				primerErrorGlobal.focus();
			}

			return; // ⛔ Detener submit
		}
	}


	// 💥 Validación de secuencia de fechas para el transporte (solo si es PLANIFICACION y estamos en Región XII)
	if (window.contextoVista?.esPlanificacion && window.regionOperativa === REGION_XII) {

		let hayErrores = false;
		let primerErrorGlobal = null;

		$('.retiro-region-xii').filter(function () {									// Buscamos todos los selectorRegion del contexto regional activo
			return $(this).closest('.retiro-item-template').length === 0;				// pero omitimos el del Template
		}).each(function () {

			// Si el SWITCH está CHECKED (TRUE) es REPOSICION y si es FALSE es RETIRO NORMAL
			const checkReposicion = $(this).find('.tipo_operacion').prop('checked');
			if (!checkReposicion) {														// La validación de fechas se efectúa si es RETIRO (NO es REPOSICION)
				const error = validarSecuenciaFechas($(this));

				if (error) {
					hayErrores = true;
					if (!primerErrorGlobal) primerErrorGlobal = error;
				}
			}

		});

		if (hayErrores) {
			e.preventDefault();

			if (primerErrorGlobal) {
				primerErrorGlobal.reportValidity();
				primerErrorGlobal.focus();
			}

			return; // ⛔ Detener submit
		}
	}

    // 💣 Eliminar template vacío si existe
    if ($('.retiro-item-template').length) {            // 💣 Si existe el Template ...
        $('.retiro-item-template').remove();            // lo removemos preventivamente (porque esta vacío y no aporta)
    }

	// 🧩 Sincronizar campos visibles → hidden (requiere_reposicion, cantidad_bins y tiene_restriccion)
	let selectorRegion = null;

	switch (window.regionOperativa) {
		case REGION_X:
			selectorRegion = '.retiro-region-x';
			break;

		case REGION_XII:
			selectorRegion = '.retiro-region-xii';
			break;

		default:
			console.warn('⚠️ Región operativa no soportada:', window.regionOperativa);
			return; // ⛔ NO sincronizamos nada
	}

	$(selectorRegion).filter(function () {											// Buscamos todos los selectorRegion del contexto regional activo
		return $(this).closest('.retiro-item-template').length === 0;				// pero omitimos el del Template
	}).each(function () {
		const $item = $(this);

		const index = $item.closest('.retiro-item-wrapper').data('index');			// Obtenemos index del retiro
		if (typeof index === 'undefined') {
			console.warn('submit> index indefinido (se omite).', $item);
			return;
		}

		const $kilogramosEstimados = $item.find(`#kilogramos_estimados_${index}`);
		const $hiddenKilogramosEstimados = $item.find(`#kilogramos_estimados_hidden_${index}`);
		const $requiereReposicion = $item.find(`#requiere_reposicion_${index}`);
		const $hiddenRequiereReposicion = $item.find(`#requiere_reposicion_hidden_${index}`);
		const $cantidad = $item.find(`#cantidad_bins_${index}`);
		const $hiddenCantidad = $item.find(`#cantidad_bins_hidden_${index}`);
		const $tipoOperacion = $item.find(`#tipo_operacion_${index}`);
		const $hiddenTipoOperacion = $item.find(`#tipo_operacion_hidden_${index}`);

		// console.log('- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
		// console.log('onSubmit> INDEX                       :', index);
		// console.log('onSubmit> kilogramos estimados VISIBLE :', $kilogramosEstimados.val());
		// console.log('onSubmit> kilogramos estimados  HIDDEN :', $hiddenKilogramosEstimados.val());
		// console.log('onSubmit> requiere reposición  VISIBLE :', $requiereReposicion.prop('checked'));
		// console.log('onSubmit> requiere reposición   HIDDEN :', $hiddenRequiereReposicion.val());
		// console.log('onSubmit> cantidad             VISIBLE :', $cantidad.val());
		// console.log('onSubmit> cantidad              HIDDEN :', $hiddenCantidad.val());
		// console.log('onSubmit> tipoOperacion        VISIBLE :', $tipoOperacion.prop('checked'));
		// console.log('onSubmit> tipoOperacion         HIDDEN :', $hiddenTipoOperacion.val());

		// Verificación del par requiereReposicion (visible y hidden) antes de usar .is(':checked')
		if ($requiereReposicion.length && $hiddenRequiereReposicion.length) {
			const valorCheck = $requiereReposicion.is(':checked') ? '1' : '0';
			$hiddenRequiereReposicion.val(valorCheck);
		} else {
			console.warn(`⚠️ Missing requiere_reposicion campos en index ${index}`);
		}

		// Verificación del par cantidad (visible y hidden) antes de usar .val()
		if ($cantidad.length && $hiddenCantidad.length) {
			const valorCantidad = $cantidad.val();
			$hiddenCantidad.val(valorCantidad);
		} else {
			console.warn(`⚠️ Missing cantidad_bins campos en index ${index}`);
		}

		// Sólo si estamos en Región XII debemos verificar la existencia y asegurar consistencia de estos pares ...
		if (window.regionOperativa === REGION_XII) {

			// Verificación del par kilogramos estimados (visible y hidden) antes de usar .val()
			if ($kilogramosEstimados.length && $hiddenKilogramosEstimados.length) {
				const valorKilos = $kilogramosEstimados.val();
				$hiddenKilogramosEstimados.val(valorKilos);
			} else {
				console.warn(`⚠️ Missing kilogramos_estimados campos en index ${index}`);
			}

			// Verificación del par tipoOperacion (visible y hidden) antes de usar prop('checked')
			if ($tipoOperacion.length && $hiddenTipoOperacion.length) {
				const valorCheck = $tipoOperacion.prop('checked') ? '1' : '0';
				$hiddenTipoOperacion.val(valorCheck);
			} else {
				console.warn(`⚠️ Missing tipo_operacion campos en index ${index}`);
			}

		}

		const checkReposicion = $(this).find('.tipo_operacion').prop('checked');			// Si el SWITCH está CHECKED (TRUE) es REPOSICION y si es FALSE es RETIRO NORMAL
		const tienePlanificacion = $item.find('.detalle-planificacion').length > 0;

		if (!checkReposicion && tienePlanificacion) {										// La validación se efectúa si es RETIRO y TIENE PLANIFICACION
			const $tieneRestriccion = $item.find(`#tiene_restriccion_${index}`);
			const $hiddenTieneRestriccion = $item.find(`#tiene_restriccion_hidden_${index}`);

			// Verificación del par cantidad (visible y hidden) antes de usar is(':checked')
			if ($tieneRestriccion.length && $hiddenTieneRestriccion.length) {
				const valorCheck = $tieneRestriccion.is(':checked') ? '1' : '0';
				$hiddenTieneRestriccion.val(valorCheck);
			} else {
				console.warn(`⚠️ Missing tiene_restriccion campos en index ${index}`);
			}

			// Y si estamos en Región XII debemos forzar preventivamente la habilitación del campo duración...
			if (window.regionOperativa === REGION_XII) {
				const $duracion = $item.find(`#duracion_estimada_dias_${index}`);
				$duracion
					.prop('disabled', false)						// Porque si el TipoTransporte es Barcaza debió quedar disabled y así no llega al BACK
					.prop('readonly', true)							// Pero lo bloqueamos a nivel de Front, para que el usuario no lo pueda modificar
					.addClass('bg-secondary-subtle text-muted')		// 👈 opcional: feedback visual tipo Bootstrap
					.css('cursor', 'not-allowed');					// 👈 opcional: el cursor refuerza la intención
			}
		}
	});

    // ⛔ Deshabilita botón de submit y pone spinner
    // Para deshabilitar el botón submit inmediatamente tras el primer envío.
    const $btnSubmit = $('#btnCrearSolicitud');
    if ($btnSubmit.length) {
        // Deshabilita el botón para evitar segundo click
        $btnSubmit.prop('disabled', true);
        $btnSubmit.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
    }

    const $btnSubmitAct = $('#btnActualizar');
    if ($btnSubmitAct.length) {
        // Deshabilita el botón para evitar segundo click
        $btnSubmitAct.prop('disabled', true);
        $btnSubmitAct.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
    }

    // 🧷 Flag para evitar siguiente submit
    this.enviado = true;

	// 📦 Log de valores enviados SOLO del contexto activo (opcional para debugging)
	// $(selectorRegion)
	// 	.find('input[name], select[name], textarea[name]')
	// 	.each(function () {

	// 		const $el = $(this);
	// 		const name = $el.attr('name');
	// 		let valor;

	// 		if ($el.is(':checkbox')) {
	// 			valor = $el.prop('checked'); // true / false
	// 		} else {
	// 			valor = $el.val();
	// 		}

	// 		console.log(`[${name}] =`, valor);
	// 	});

	// alert('ENTER para continuar. Revisa consola para detalles.');
});

// Determina si estamos en un rebote desde el back con datos previos (old).
function esReboteDesdeBack() {
    return (
        window.oldValues &&                                                     // Requiere que oldValues exista ... y tenga todos los arrays esperados.
        Array.isArray(window.oldValues.tipo_retiro) &&                          // Este es necesario para el toggleTipoRetiro
        Array.isArray(window.oldValues.requiere_reposicion_hidden) &&           // Este es necesario para pintar blade
        Array.isArray(window.oldValues.cantidad_bins_hidden) &&                 // Este es necesario para pintar blade

		Array.isArray(window.oldValues.kilogramos_estimados_hidden) &&          // Este es necesario para pintar blade
        Array.isArray(window.oldValues.tipo_operacion_hidden) &&                // Este es necesario para pintar blade
        Array.isArray(window.oldValues.tiene_restriccion_hidden)                // Este es necesario para pintar blade
    );
}
// Bandera global accesible desde otros scripts
window.esRebote = esReboteDesdeBack();

// Agregar un valor por defecto al final de los 3 arrays
function agregarOldRetiro() {
	if (!window.esRebote) return;

	window.oldValues.tipo_retiro.push('0');                                      // Default: tipo no definido - Este es necesario para el toggleTipoRetiro
	window.oldValues.requiere_reposicion_hidden.push('0');                       // Default: no requiere - Este es necesario para pintar blade
	window.oldValues.cantidad_bins_hidden.push('0');                             // Default: cero bins - Este es necesario para pintar blade

	window.oldValues.kilogramos_estimados_hidden.push('0');                      // Default: cero bins - Este es necesario para pintar blade
	window.oldValues.tipo_operacion_hidden.push('0');                            // Default: no requiere - Este es necesario para pintar blade
	window.oldValues.tiene_restriccion_hidden.push('0');                         // Default: no requiere - Este es necesario para pintar blade
}

// Eliminar un valor por índice específico en los 3 arrays
function eliminarOldRetiro(index) {
    if (!window.esRebote) return;

    window.oldValues.tipo_retiro.splice(index, 1);                               // Este es necesario para el toggleTipoRetiro
    window.oldValues.requiere_reposicion_hidden.splice(index, 1);                // Este es necesario para pintar blade
    window.oldValues.cantidad_bins_hidden.splice(index, 1);                      // Este es necesario para pintar blade

	window.oldValues.kilogramos_estimados_hidden.splice(index, 1);               // Este es necesario para pintar blade
    window.oldValues.tipo_operacion_hidden.splice(index, 1);                     // Este es necesario para pintar blade
    window.oldValues.tiene_restriccion_hidden.splice(index, 1);                  // Este es necesario para pintar blade
}
