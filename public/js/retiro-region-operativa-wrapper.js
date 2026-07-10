/*
 * Función que activa la estructura válida del Partial Blade : Región X o Región XII
 *
 */
function aplicarRegionOperativa(regionOpe) {
	$('.retiro-item-wrapper').filter(function () {											// Buscamos todos los wrappers (cada uno con ambos contextos regionales)
		return $(this).closest('.retiro-item-template').length === 0;						// Pero omitimos el que es del template
	}).each(function () {
        const $wrapper = $(this);
		let $retiroContexto = null;

		// Si es edición de planificación NO se puede editar la región de la Solicitud del Retiro
		const puedeEditarSolicitud = !(window.contextoVista.esPlanificacion && window.contextoVista.esEdit);

		// Si el estado es PROGRAMADO no puede editar las fechas de planificación, zarpe ni arribo
		const puedeEditarFechasPlanificacion = !( window.estadoSolicitud == ESTADO_RETIRO_PROGRAMADO );

		// Decide el contexto regional
        switch (regionOpe) {
            case REGION_X:
				// 🔒 Deshabilitar SOLO región XII
                $wrapper.find('.retiro-region-xii')
						.addClass('d-none')
						.find('input, select, textarea')
						.prop('disabled', true)
                        .removeAttr('required');

				// Calculamos el contexto
				$retiroContexto = $wrapper.find('.retiro-region-x');

                // ✅ Habilitar región X
				$wrapper.find('.retiro-region-x')
						.removeClass('d-none');

				$wrapper.find('.retiro-region-x .fecha_retiro').prop('required', true);							// Aquí SOLO reponer required donde corresponde
				$wrapper.find('.retiro-region-x .kilogramos_estimados').prop('required', true);

				// Habilitamos sólo lo que corresponde
				if (puedeEditarSolicitud) {																		// No estamos editando una planificaicón
					$wrapper.find('.retiro-region-x .fecha_retiro').prop('disabled', false);
					$wrapper.find('.retiro-region-x .tipo_retiro').prop('disabled', false);
					$wrapper.find('.retiro-region-x .kilogramos_estimados').prop('disabled', false);
					$wrapper.find('.retiro-region-x .requiere_reposicion_hidden').prop('disabled', false);		// 🔓 Re-habilitar hidden fields requeridos para backend
					$wrapper.find('.retiro-region-x .cantidad_bins_hidden').prop('disabled', false);			// 🔓 Re-habilitar hidden fields requeridos para backend
				}

				$wrapper.find('.retiro-region-x .fecha_planificada').prop('disabled', false);
				$wrapper.find('.retiro-region-x .duracion_viaje').prop('disabled', false);
				$wrapper.find('.retiro-region-x .hora_llegada_estimada_hidden').prop('disabled', false);		// 🔓 Re-habilitar hidden fields requeridos para backend

				$wrapper.find('.retiro-region-x .tipo_materia_prima').prop('disabled', false);
				$wrapper.find('.retiro-region-x .especie').prop('disabled', false);
				$wrapper.find('.retiro-region-x .tiene_restriccion').prop('disabled', false);
				$wrapper.find('.retiro-region-x .tiene_restriccion_hidden').prop('disabled', false);

				$wrapper.find('.retiro-region-x .patente_camion').prop('disabled', false);
				$wrapper.find('.retiro-region-x .patente_rampla').prop('disabled', false);

				// Disparar el evento change del camión de retiro
				$wrapper.find('.retiro-region-x .patente_camion').trigger('change');
                break;

            case REGION_XII:
                // 🔒 Deshabilitar SOLO región X
                $wrapper.find('.retiro-region-x')
                        .addClass('d-none')
						.find('input, select, textarea')
                        .prop('disabled', true)
                        .removeAttr('required');

				// Calculamos el contexto
				$retiroContexto = $wrapper.find('.retiro-region-xii');

				// ✅ Habilitar región XII
				$wrapper.find('.retiro-region-xii')
						.removeClass('d-none');

				$wrapper.find('.retiro-region-xii .fecha_retiro').prop('required', true); 							// Aquí SOLO reponer required donde corresponde

				// Si el SWITCH está CHECKED (TRUE) es REPOSICION y si es FALSE es RETIRO NORMAL
				const checkReposicion = $wrapper.find('.retiro-region-xii .tipo_operacion').prop('checked');

				// Habilitamos sólo lo que corresponde
				if (puedeEditarSolicitud) {																			// No estamos editando una planificaicón

					$wrapper.find('.retiro-region-xii .fecha_retiro').prop('disabled', false);
					$wrapper.find('.retiro-region-xii .tipo_retiro').prop('disabled', false);
					$wrapper.find('.retiro-region-xii .kilogramos_estimados').prop('disabled', checkReposicion);	// Si el SWITCH está checked (true) -> Disabled los Kilos (y vice versa)
					$wrapper.find('.retiro-region-xii .kilogramos_estimados_hidden').prop('disabled', false);		// 🔓 Re-habilitar hidden fields requeridos para backend

					$wrapper.find('.retiro-region-xii .requiere_reposicion_hidden').prop('disabled', false);		// 🔓 Re-habilitar hidden fields requeridos para backend
					$wrapper.find('.retiro-region-xii .cantidad_bins_hidden').prop('disabled', false);				// 🔓 Re-habilitar hidden fields requeridos para backend
					$wrapper.find('.retiro-region-xii .tipo_operacion').prop('disabled', false);
					$wrapper.find('.retiro-region-xii .tipo_operacion_hidden').prop('disabled', false);				// 🔓 Re-habilitar hidden fields requeridos para backend
				}

				if (puedeEditarFechasPlanificacion) {
					$wrapper.find('.retiro-region-xii .fecha_planificada').prop('disabled', false);					// ESTA SI ES FALSE para que este HABILITADA incluso cuando es REPOSICION

					const $fechaArriboPuerto = $wrapper.find('.retiro-region-xii .fecha_arribo_puerto');
					if ($fechaArriboPuerto.val()) {
						$wrapper.find('.retiro-region-xii .duracion_estimada_dias').prop('disabled', true);
					}
					else {
						$wrapper.find('.retiro-region-xii .duracion_estimada_dias').prop('disabled', checkReposicion);		// Aquií depende de que NO sea REPOSICION
					}

					$wrapper.find('.retiro-region-xii .tipo_transporte').prop('disabled', checkReposicion);					// Aquií depende de que NO sea REPOSICION
				}
				else {
					$wrapper.find('.retiro-region-xii .fecha_planificada').prop('disabled', true);
					$wrapper.find('.retiro-region-xii .duracion_estimada_dias').prop('disabled', true);
					$wrapper.find('.retiro-region-xii .tipo_transporte').prop('disabled', true);
				}

				$wrapper.find('.retiro-region-xii .eta_calculada').prop('disabled', checkReposicion);			// 🔓 Re-habilitar el campo para que puedan modificar la hora (controlado por JS)
				$wrapper.find('.retiro-region-xii .eta_calculada_hidden').prop('disabled', false);				// 🔓 Re-habilitar hidden fields requeridos para backend

				$wrapper.find('.retiro-region-xii .tipo_materia_prima').prop('disabled', checkReposicion);		// Aquií depende de que NO sea REPOSICION
				$wrapper.find('.retiro-region-xii .especie').prop('disabled', checkReposicion);					// Aquií depende de que NO sea REPOSICION
				$wrapper.find('.retiro-region-xii .tiene_restriccion').prop('disabled', checkReposicion);		// Aquií depende de que NO sea REPOSICION

				$wrapper.find('.retiro-region-xii .tiene_restriccion_hidden').prop('disabled', false);

				$wrapper.find('.retiro-region-xii .patente_rampla').prop('disabled', checkReposicion);			// Aquií depende de que NO sea REPOSICION
				$wrapper.find('.retiro-region-xii .estado_rampla').prop('disabled', checkReposicion);			// Aquií depende de que NO sea REPOSICION
				$wrapper.find('.retiro-region-xii .patente_camion').prop('disabled', checkReposicion);			// Aquií depende de que NO sea REPOSICION

				$wrapper.find('.retiro-region-xii .camion_rescate').prop('disabled', checkReposicion);			// Aquií depende de que NO sea REPOSICION

				if (checkReposicion) {
					// Ocultamos todos los elementos y grupos que no son necesarios
					$wrapper.find('.retiro-region-xii .campo_duracion_estimada_dias').addClass('d-none');
					$wrapper.find('.retiro-region-xii .campo_eta_calculada').addClass('d-none');

					$wrapper.find('.retiro-region-xii .bloque_especificaciones').addClass('d-none');

					$wrapper.find('.retiro-region-xii .bloque_transporte').addClass('d-none');
					$wrapper.find('.retiro-region-xii .bloque_rescate').addClass('d-none');
				}
				else {
					// Hacemos aparecer aquellas secciones ocultas
					$wrapper.find('.retiro-region-xii .campo_duracion_estimada_dias').removeClass('d-none');
					$wrapper.find('.retiro-region-xii .campo_eta_calculada').removeClass('d-none');

					$wrapper.find('.retiro-region-xii .bloque_especificaciones').removeClass('d-none');

					$wrapper.find('.retiro-region-xii .bloque_transporte').removeClass('d-none');
					$wrapper.find('.retiro-region-xii .bloque_rescate').removeClass('d-none');

					// Si estamos en creación PLANIFICACION : Creacíon manual o edición
					if (window.contextoVista?.esPlanificacion) {
						// ⚠️ toggleTipoTransporte aquí es invocado por sincronización de contexto,
						// Recalcular estados de rampla si vamos a Editar la Planificación.
						// Debe actualizar estados por inicialización de contexto.
						toggleTipoTransporte($retiroContexto);

						// Disparar el evento change de los camiones de retiro y rescate
						$wrapper.find('.retiro-region-xii .patente_camion').trigger('change');
						$wrapper.find('.retiro-region-xii .camion_rescate').trigger('change');
					}
				}
                break;

			default:	// Si no es X o XII deja ambas ocultas
				console.warn('aplicarRegionOperativa> region operativa indefinida (se omite).', $retiro);
				return;
		}

		// Si no estamos EDITANDO una PLANIFICACION.
		// Repasamos los form-control de la sección de SOLICITUD de RETIRO
		if (puedeEditarSolicitud) {
			// 3° Sincronización dependiente del contexto activo.
			// Se debe hacer siempre y para ambos contextos (Región X y Región XII)
			if ($retiroContexto && $retiroContexto.length) {

				// 1️⃣ Aplica reglas de tipo retiro (esto setea el checkbox en rebote)
				toggleTipoRetiro($retiroContexto);

				// 2️⃣ REEJECUTA explícitamente el change del checkbox
				const $requiere = $retiroContexto.find('.requiere_reposicion');
				if ($requiere.length) {
					$requiere.trigger('change');
				}

				// 3️⃣ Ahora sí la cantidad se habilita correctamente
				toggleCantidadBins($retiroContexto);
			}
		}
	});
}
