// Suma horas y minutos a una fecha base y devuelve string compatible con input[type=datetime-local]
function calcularFechaMasHoras(fechaBase, duracionHHMM) {
    if (!fechaBase || !duracionHHMM) return null;

    const fecha = (fechaBase instanceof Date)
        ? new Date(fechaBase)
        : new Date(fechaBase);

    if (isNaN(fecha.getTime())) return null;

    const partes = String(duracionHHMM).split(':');
    if (partes.length !== 2) return null;

    const horas   = Number(partes[0]);
    const minutos = Number(partes[1]);

    if (isNaN(horas) || isNaN(minutos)) return null;

    // Sumar horas y minutos
    fecha.setHours(fecha.getHours() + horas);
    fecha.setMinutes(fecha.getMinutes() + minutos);

    const pad = n => String(n).padStart(2, '0');

    return (
        fecha.getFullYear() + '-' +
        pad(fecha.getMonth() + 1) + '-' +
        pad(fecha.getDate()) + 'T' +
        pad(fecha.getHours()) + ':' +
        pad(fecha.getMinutes())
    );
}
// Suma días a una fecha base y devuelve string compatible con input[type=datetime-local]
function calcularFechaMasDias(fechaBase, diasASumar) {
    if (!fechaBase || isNaN(diasASumar)) return null;

    const fecha = (fechaBase instanceof Date)
        ? new Date(fechaBase)
        : new Date(fechaBase);

    if (isNaN(fecha.getTime())) return null;

    fecha.setDate(fecha.getDate() + Number(diasASumar));

    const pad = n => String(n).padStart(2, '0');

    return (
        fecha.getFullYear() + '-' +
        pad(fecha.getMonth() + 1) + '-' +
        pad(fecha.getDate()) + 'T' +
        pad(fecha.getHours()) + ':' +
        pad(fecha.getMinutes())
    );
}
// Diferencia en días entre dos fechas
function calcularDiferenciaEnDias(fechaInicio, fechaFin) {
    if (!fechaInicio || !fechaFin) return null;

    const f1 = (fechaInicio instanceof Date)
        ? new Date(fechaInicio)
        : new Date(fechaInicio);

    const f2 = (fechaFin instanceof Date)
        ? new Date(fechaFin)
        : new Date(fechaFin);

    if (isNaN(f1.getTime()) || isNaN(f2.getTime())) return null;

    // Normalizamos a medianoche para evitar errores por horas/minutos
    f1.setHours(0, 0, 0, 0);
    f2.setHours(0, 0, 0, 0);

    const MS_POR_DIA = 1000 * 60 * 60 * 24;

    return Math.round((f2 - f1) / MS_POR_DIA);
}

// Manejo de subordinación de fecha de planificación a la fecha solicitada para el retiro
function sincronizaHoraPlanificada($bloque) {
    const fechaRetiro = $bloque.find('.fecha_retiro')[0];
    const fechaPlanificacion = $bloque.find('.fecha_planificada')[0];

    if (!fechaRetiro || !fechaPlanificacion) return;

    const valor = fechaRetiro.value;

    // Sólo si hay valor válido lo replicamos
    if (valor && valor.length > 0) {
        fechaPlanificacion.value = valor;
        $(fechaPlanificacion).trigger('change'); // Dispara cambio por si hay lógica atada (ej. recalcular ETA sumando tiempo de viaje)
    }
}
// Sincroniza HoraPlanificada con la HoraSolicitada
$(document).on('change', '.fecha_retiro', function () {
    sincronizaHoraPlanificada($(this).closest('.retiro-region'));
});


function extraerFechaYHora(str) {
    if (!str || !str.includes('T')) {
        return { fecha: null, hora: null };
    }

    const [fecha, hora] = str.split('T');
    return { fecha, hora };
}
$(document).on('change', '.eta_calculada', function () {
    const $input = $(this);
    const $bloque = $input.closest('.retiro-region');
    const $hidden = $bloque.find('.eta_calculada_hidden');

    const valorActual = $input.val();
    const base = $input.data('eta-base');

	if (!valorActual) return;

	// Si no hay base aún, inicializarla
    if (!base) {
        $input.data('eta-base', valorActual);
        if ($hidden.length) $hidden.val(valorActual);
        return;
    }

	const baseParts = extraerFechaYHora(base);
	const actualParts = extraerFechaYHora(valorActual);

    // Si cambió la fecha → revertir SOLO la fecha
	if (baseParts.fecha && actualParts.fecha && baseParts.fecha !== actualParts.fecha && actualParts.hora) {

        const corregido = baseParts.fecha + 'T' + actualParts.hora;
        $input.val(corregido);
        if ($hidden.length) $hidden.val(corregido);

		// 👈 IMPORTANTE: actualizar base coherente
        // $input.data('eta-base', corregido);

    } else {

		// Solo cambió la hora → se permite
        $input.val(valorActual);
		if ($hidden.length) $hidden.val(valorActual);

		// 👈 base ahora evoluciona
		// $input.data('eta-base', valorActual);
	}
});


// Función que calcula hora de llegada a partir de un bloque específico.
// APTO para ambas regiones : Región X y Región XII
function calcularHoraLlegada($bloque) {
	// const idxBloque = $bloque.attr('data-index');
	// console.log('En calcularHoraLlegada - Procesando :', idxBloque);

	switch (window.regionOperativa) {
		case REGION_X:
			// Obtener los elementos necesarios usando clases
			const $fechaHora_planificada    = $bloque.find('.fecha_planificada');
			const $duracion_viaje           = $bloque.find('.duracion_viaje');
			const $fechaHora_llegada        = $bloque.find('.hora_llegada');
			const $fechaHora_llegada_hidden = $bloque.find('.hora_llegada_estimada_hidden');

			const nuevaFechaHora = calcularFechaMasHoras( $fechaHora_planificada.val(), $duracion_viaje.val() );
			$fechaHora_llegada.val(nuevaFechaHora ?? '');
			if ($fechaHora_llegada_hidden.length) $fechaHora_llegada_hidden.val(nuevaFechaHora ?? '');

			break;

		case REGION_XII:
			// Obtener los elementos necesarios usando clases
			const $fecha_planificada      = $bloque.find('.fecha_planificada');
			const $duracion_estimada_dias = $bloque.find('.duracion_estimada_dias');
			const duracion                = parseInt($duracion_estimada_dias.val()) || 0;
			const $eta_calculada          = $bloque.find('.eta_calculada');
			const $eta_calculada_hidden   = $bloque.find('.eta_calculada_hidden');

			const nvaEta = calcularFechaMasDias($fecha_planificada.val(), duracion) ?? '';
			$eta_calculada.val(nvaEta);
			if ($eta_calculada_hidden.length) $eta_calculada_hidden.val(nvaEta);

			$eta_calculada.data('eta-base', nvaEta); // 👈 NUEVO

			break;

		default:	// Si no es X o XII no hace nada
			return;
	}
}
// 🔹 Activar en cambios de campos origen - SOLO campos que afectan el cálculo
$(document).on('change', '.fecha_planificada, .duracion_viaje, .duracion_estimada_dias',
    function () {
        const $bloque = $(this).closest('.retiro-region');
        calcularHoraLlegada($bloque);
    }
);
function actualizarEtaCalculada($bloque) {
	// Obtener los elementos necesarios usando clases
    const $fecha_arribo_puerto    = $bloque.find('.fecha_arribo_puerto');
	const $eta_calculada          = $bloque.find('.eta_calculada');
	const $eta_calculada_hidden   = $bloque.find('.eta_calculada_hidden');

	const $fecha_planificada      = $bloque.find('.fecha_planificada');
	const $duracion_estimada_dias = $bloque.find('.duracion_estimada_dias');

	const horasDelayArriboEta     = window.operationalParameters?.delayArriboEtaHours ?? 0;
	const delayHHMM               = `${String(horasDelayArriboEta).padStart(2,'0')}:00`;							// Transformar a HH:MM
	const nvaEta                  = calcularFechaMasHoras( $fecha_arribo_puerto.val(), delayHHMM );

	$eta_calculada.val(nvaEta);
	if ($eta_calculada_hidden.length) $eta_calculada_hidden.val(nvaEta);

	$eta_calculada.data('eta-base', nvaEta); // 👈 NUEVO

	// Duración en Días = (eta_calculada - fecha_planificada)
	const nvaDuracion = calcularDiferenciaEnDias($fecha_planificada.val(), $eta_calculada.val()) ?? '0'; // f2 - f1
	$duracion_estimada_dias.val(nvaDuracion);
}
// Actualiza la ETA_CALCULADA con la fecha de arribo a puerto
$(document).on('change', '.fecha_arribo_puerto', function () {
    const $bloque = $(this).closest('.retiro-region');

    const $fechaArribo = $bloque.find('.fecha_arribo_puerto');
    const $duracion    = $bloque.find('.duracion_estimada_dias');

    const tieneArribo = $fechaArribo.val() && $fechaArribo.val().length > 0;

    if (tieneArribo) {
        // 🔒 Arribo manda → deshabilitamos duración
        $duracion.prop('disabled', true);

        // Actualizamos ETA y duración derivada
        actualizarEtaCalculada($bloque);
    } else {
        // 🔓 Sin arribo → vuelve a mandar duración
        $duracion.prop('disabled', false);

        // Recalcular ETA en base a planificación + duración
        calcularHoraLlegada($bloque);
    }
});


// 🔹 HELPER transforma string de fechas en formato DD-MM-YYYY a objetos Date 'AAAA-MM-DD HH:MM:SS'
function parseFechaChile(fechaStr) {
    if (!fechaStr) return null;

    const limpio = fechaStr.trim();

    // 🧠 Caso 1: ya viene en formato ISO (datetime-local)
    if (limpio.includes('T')) {
        const fechaISO = new Date(limpio);
        return isNaN(fechaISO) ? null : fechaISO;
    }

    // 🧠 Caso 2: formato DD-MM-YYYY HH:mm o HH:mm:ss
    const partes = limpio.split(/\s+/);
    if (partes.length < 2) return null;

    const [fechaPart, horaPart] = partes;
    const [dia, mes, anio] = fechaPart.split('-');

    if (!dia || !mes || !anio || !horaPart) return null;

    // Soporta HH:mm o HH:mm:ss
    const fecha = new Date(`${anio}-${mes}-${dia}T${horaPart}`);

    return isNaN(fecha) ? null : fecha;
}
// 🔹 HELPER compara fechas en objetos Date 'AAAA-MM-DD HH:MM:SS' (clave para no duplicar errores)
function esMenor(fechaA, fechaB) {
    const fA = parseFechaChile(fechaA);
    const fB = parseFechaChile(fechaB);

    // ⚠️ Si no puedo evaluar → NO lo dejo pasar
    if (!fA || !fB) {
        console.warn('⚠️ Fecha inválida en comparación:', fechaA, fechaB);
        return null; // ← no false
    }

    return fA < fB;
}
// 🔹 UNA sola función de validación de coherencia entre todas las fechas con secuencia operativa
function validarSecuenciaFechas($bloque) {
    const retiro   = $bloque.find('.fecha_retiro')[0];
    const plan     = $bloque.find('.fecha_planificada')[0];
    const embarque = $bloque.find('.fecha_embarque')[0];
    const arribo   = $bloque.find('.fecha_arribo_puerto')[0];
    const eta      = $bloque.find('.eta_calculada')[0];

    // Limpiar errores previos
    [retiro, plan, embarque, arribo, eta].forEach(el => {
        if (el) el.setCustomValidity('');
    });

    // Reglas
	let primerError = null;

	// 1. Planificación vs Retiro
	if (retiro && plan && plan.value && retiro.value) {
		const cmp = esMenor(plan.value, retiro.value);

		if (cmp === true) {
			plan.setCustomValidity('La planificación no puede ser anterior al retiro.');
			if (!primerError) primerError = plan;
		} else if (cmp === null) {
			plan.setCustomValidity('Formato de fecha inválido.');
			if (!primerError) primerError = plan;
		}
	}

	// 2. Embarque vs Planificación
	if (plan && embarque && embarque.value && plan.value) {
		const cmp = esMenor(embarque.value, plan.value);

		if (cmp === true) {
			embarque.setCustomValidity('El embarque no puede ser anterior a la planificación.');
			if (!primerError) primerError = embarque;
		} else if (cmp === null) {
			embarque.setCustomValidity('Formato de fecha inválido.');
			if (!primerError) primerError = embarque;
		}
	}

	// 3. Arribo vs Embarque
	if (embarque && arribo && arribo.value && embarque.value) {
		const cmp = esMenor(arribo.value, embarque.value);

		if (cmp === true) {
			arribo.setCustomValidity('El arribo no puede ser anterior al embarque.');
			if (!primerError) primerError = arribo;
		} else if (cmp === null) {
			arribo.setCustomValidity('Formato de fecha inválido.');
			if (!primerError) primerError = arribo;
		}
	}

	// 4. ETA vs Arribo
	if (arribo && eta && eta.value && arribo.value) {
		const cmp = esMenor(eta.value, arribo.value);

		if (cmp === true) {
			eta.setCustomValidity('La ETA no puede ser anterior al arribo.');
			if (!primerError) primerError = eta;
		} else if (cmp === null) {
			eta.setCustomValidity('Formato de fecha inválido.');
			if (!primerError) primerError = eta;
		}
	}

	return primerError;
}
// 🔹 TODOS los campos de fechas con secuencia operativa se validan pero después mostramos SOLO lo relevante
$(document).on('change', '.fecha_planificada, .eta_calculada, .fecha_embarque, .fecha_arribo_puerto',
    function () {
        const $bloque = $(this).closest('.retiro-region');
        const inputConError = validarSecuenciaFechas($bloque);
        if (inputConError) {
            inputConError.reportValidity(); // 👈 muestra tooltip nativo
        }
    }
);


// Manejo de subordinación de fechas de zarpado y arribo del transporte maritimo
function formatDatetimeLocal(date) {
    const pad = n => String(n).padStart(2,'0');

    return date.getFullYear() + '-' +
           pad(date.getMonth()+1) + '-' +
           pad(date.getDate()) + 'T' +
           pad(date.getHours()) + ':' +
           pad(date.getMinutes());
}
function toggleTipoTransporte($bloque) {
	// Obtenemos elementos Duración de Viaje, ETA, Tipo de Transporte, fecha de Embarque/Arribo a puerto y Rescates
	const $fecha_planificada      = $bloque.find('.fecha_planificada');

	const $duracion_estimada_dias = $bloque.find('.duracion_estimada_dias');
	const duracionOriginal        = parseInt($duracion_estimada_dias.data('original')) || 0;

	const $eta_calculada          = $bloque.find('.eta_calculada');
	const $eta_calculada_hidden   = $bloque.find('.eta_calculada_hidden');
	const etaOriginalRaw          = $eta_calculada.data('original');
	const etaOriginal             = etaOriginalRaw ? new Date(etaOriginalRaw) : null;

	const $tipoTransporte         = $bloque.find('.tipo_transporte');
    let tipoTransporteId          = parseInt($tipoTransporte.val()) || 0;
	const $tipoTransporteActual   = $bloque.find('.tipo_transporte_actual');
    const tipoTransporteIdActual  = parseInt($tipoTransporteActual.val()) || 0;

	const $fechaEmbarque          = $bloque.find('.fecha_embarque');
	const $fechaArriboPuerto      = $bloque.find('.fecha_arribo_puerto');
	const $grupo_cabotaje         = $bloque.find('.grupo_cabotaje');

	const $estadoRampla           = $bloque.find('.estado_rampla');

	const $fecha_rescate_puerto   = $bloque.find('.fecha_rescate_puerto');
	const $camion_rescate         = $bloque.find('.camion_rescate');
	const $conductor_rescate      = $bloque.find('.conductor_rescate');

	const $bloque_rescate         = $bloque.find('.bloque_rescate');

	const diasMar                 = window.operationalParameters?.maritimeTransitDurationDays ?? 0;
	const diasTierra              = window.operationalParameters?.terrestrialTransitDurationDays ?? 0;
	const diasCombinado           = window.operationalParameters?.combinedTransitDurationDays ?? 0;

	// Si el estado es PROGRAMADO no puede editar las fechas (planificación, zarpe ni arribo) ni tampoco DURACION
	const puedeEditarFechasPlanificacion = !( window.estadoSolicitud == ESTADO_RETIRO_PROGRAMADO );

	if (window.regionOperativa == REGION_XII) {
		if((!window.listenerTipoTransporte)  && (isNaN(tipoTransporteId) || tipoTransporteId === 0)) {
			// console.log('NO FUE Listener y DEBO copiar tipoTransporteIdActual -> tipoTransporteId ...');
			tipoTransporteId = tipoTransporteIdActual;
		}

		switch (tipoTransporteId) {
			case TIPO_TRANSPORTE_TIERRA:											// Sólo cuando es por tierra se deshabilitan todos los campos
				// Habilita duración
				$duracion_estimada_dias.prop('disabled', false);

				if (tipoTransporteId == tipoTransporteIdActual) {
					$duracion_estimada_dias.val(duracionOriginal);

					if (etaOriginal) {
						$eta_calculada.val(formatDatetimeLocal(etaOriginal));

						// 👈 Base evoluciona - Para posterior restricción en ETA al cambio de fecha y sólo permitir cambio de hora
						$eta_calculada.data('eta-base', $eta_calculada.val());
					}
				}
				else {
					const etaActual = $eta_calculada.val();
					const etaBase = $eta_calculada.data('eta-base');
					const usuarioModificoHora = etaActual && etaBase && (etaActual !== etaBase);

					if (!usuarioModificoHora) {
						$duracion_estimada_dias.val(diasTierra);

						const nvaEta = calcularFechaMasDias($fecha_planificada.val(), diasTierra) ?? '';
						$eta_calculada.val(nvaEta);
						if ($eta_calculada_hidden.length) $eta_calculada_hidden.val(nvaEta);

						// 👈 Base evoluciona - Para posterior restricción en ETA al cambio de fecha y sólo permitir cambio de hora
						$eta_calculada.data('eta-base', $eta_calculada.val());
					}
				}

				// Deshabilita fechas de cabotaje
				$fechaEmbarque.prop('disabled', true).val('');
				$fechaArriboPuerto.prop('disabled', true).val('');
				$grupo_cabotaje.addClass('d-none');

				// Deshabilita campos de rescate
				$fecha_rescate_puerto.prop('disabled', true).val('');
				$camion_rescate.val('').prop('disabled', true).trigger('change.select2');
				$camion_rescate.select2({ placeholder: '' });
				$conductor_rescate.val('').prop('disabled', true).trigger('change.select2');
				$conductor_rescate.select2({ placeholder: '' });

				$bloque_rescate.addClass('d-none');
				break;

			case TIPO_TRANSPORTE_BARCAZA:
				// Habilita duración del viaje - Sujeto a que no sea estado PROGRAMADO
				if (puedeEditarFechasPlanificacion) {

					const $fechaArriboPuerto = $bloque.find('.fecha_arribo_puerto');
					if ($fechaArriboPuerto.val()) {
						$duracion_estimada_dias.prop('disabled', true);
					}
					else {
						$duracion_estimada_dias.prop('disabled', false);
					}

				}
				else {
					$duracion_estimada_dias.prop('disabled', true);
				}

				if (tipoTransporteId == tipoTransporteIdActual) {
					$duracion_estimada_dias.val(duracionOriginal);

					if (etaOriginal) {
						$eta_calculada.val(formatDatetimeLocal(etaOriginal));

						// 👈 Base evoluciona - Para posterior restricción en ETA al cambio de fecha y sólo permitir cambio de hora
						$eta_calculada.data('eta-base', $eta_calculada.val());
					}
				}
				else {
					const etaActual = $eta_calculada.val();
					const etaBase   = $eta_calculada.data('eta-base');
					const usuarioModificoHora = etaActual && etaBase && (etaActual !== etaBase);

					if (!usuarioModificoHora) {
						// Antes de recalcular ETA vemos si tenemos fecha arribo
						const $fechaArriboPuerto = $bloque.find('.fecha_arribo_puerto');

						// Hay fecha arribo : nvaETA = fecha_arribo + DELAY
						let nvaEta = '';
						if ($fechaArriboPuerto.val()) {
							const horasDelayArriboEta = window.operationalParameters?.delayArriboEtaHours ?? 0;
							const delayHHMM           = `${String(horasDelayArriboEta).padStart(2,'0')}:00`;				// Transformar a HH:MM
							nvaEta              = calcularFechaMasHoras( $fechaArriboPuerto.val(), delayHHMM );

						} else {																							// NO hay fecha arribo : ETA = fecha_planificacion + DURACION
							$duracion_estimada_dias.val(diasMar);
							nvaEta = calcularFechaMasDias($fecha_planificada.val(), diasMar) ?? '';
						}

						// Asignamos nuvaEta
						$eta_calculada.val(nvaEta);
						if ($eta_calculada_hidden.length) $eta_calculada_hidden.val(nvaEta);

						// 👈 Base evoluciona - Para posterior restricción en ETA al cambio de fecha y sólo permitir cambio de hora
						$eta_calculada.data('eta-base', $eta_calculada.val());
					}
				}

				// Habilita fechas de zarpado y arribo - Sujeto a que no sea estado PROGRAMADO
				if (puedeEditarFechasPlanificacion) {
					$fechaEmbarque.prop('disabled', false);
					$fechaArriboPuerto.prop('disabled', false);
				}
				else {
					$fechaEmbarque.prop('disabled', true);
					$fechaArriboPuerto.prop('disabled', true);
				}

				$grupo_cabotaje.removeClass('d-none');

				// Deshabilita campos de rescate desde puerto
				$fecha_rescate_puerto.prop('disabled', true).val('');
				$camion_rescate.val('').prop('disabled', true).trigger('change.select2');
				$camion_rescate.select2({ placeholder: '' });
				$conductor_rescate.val('').prop('disabled', true).trigger('change.select2');
				$conductor_rescate.select2({ placeholder: '' });

				$bloque_rescate.addClass('d-none');
				break;

			case TIPO_TRANSPORTE_COMBINADO:											// Sólo el combinado usa barcaza y camión de rescate
				// Habilita duración del viaje - Sujeto a que no sea estado PROGRAMADO
				if (puedeEditarFechasPlanificacion) {

					const $fechaArriboPuerto = $bloque.find('.fecha_arribo_puerto');
					if ($fechaArriboPuerto.val()) {
						$duracion_estimada_dias.prop('disabled', true);
					}
					else {
						$duracion_estimada_dias.prop('disabled', false);
					}

				}
				else {
					$duracion_estimada_dias.prop('disabled', true);
				}

				if (tipoTransporteId == tipoTransporteIdActual) {
					$duracion_estimada_dias.val(duracionOriginal);

					if (etaOriginal) {
						$eta_calculada.val(formatDatetimeLocal(etaOriginal));

						// 👈 Base evoluciona - Para posterior restricción en ETA al cambio de fecha y sólo permitir cambio de hora
						$eta_calculada.data('eta-base', $eta_calculada.val());
					}
				}
				else {
					const etaActual = $eta_calculada.val();
					const etaBase   = $eta_calculada.data('eta-base');
					const usuarioModificoHora = etaActual && etaBase && (etaActual !== etaBase);

					if (!usuarioModificoHora) {
						// Antes de recalcular ETA vemos si tenemos fecha arribo
						const $fechaArriboPuerto = $bloque.find('.fecha_arribo_puerto');

						// Hay fecha arribo : nvaETA = fecha_arribo + DELAY
						let nvaEta = '';
						if ($fechaArriboPuerto.val()) {
							const horasDelayArriboEta = window.operationalParameters?.delayArriboEtaHours ?? 0;
							const delayHHMM           = `${String(horasDelayArriboEta).padStart(2,'0')}:00`;				// Transformar a HH:MM
							nvaEta              = calcularFechaMasHoras( $fechaArriboPuerto.val(), delayHHMM );

						} else {																							// NO hay fecha arribo : ETA = fecha_planificacion + DURACION
							$duracion_estimada_dias.val(diasCombinado);
							nvaEta = calcularFechaMasDias($fecha_planificada.val(), diasCombinado) ?? '';
						}

						// Asignamos nuvaEta
						$eta_calculada.val(nvaEta);
						if ($eta_calculada_hidden.length) $eta_calculada_hidden.val(nvaEta);

						// 👈 Base evoluciona - Para posterior restricción en ETA al cambio de fecha y sólo permitir cambio de hora
						$eta_calculada.data('eta-base', $eta_calculada.val());
					}
				}

				// Habilita fechas de zarpado y arribo - Sujeto a que no sea estado PROGRAMADO
				if (puedeEditarFechasPlanificacion) {
					$fechaEmbarque.prop('disabled', false);
					$fechaArriboPuerto.prop('disabled', false);
				}
				else {
					$fechaEmbarque.prop('disabled', true);
					$fechaArriboPuerto.prop('disabled', true);
				}

				$grupo_cabotaje.removeClass('d-none');

				// Habilita campos de rescate desde puerto
				$bloque_rescate.removeClass('d-none');

				$fecha_rescate_puerto.prop('disabled', false);
				$camion_rescate.prop('disabled', false);
				break;

			default:																// No se selecciono Tipo de Transporte, se desabilita TODO
				// Habilitación del campo dias de duración del viaje
				$duracion_estimada_dias.prop('disabled', false);

				// Recalcular fecha ETA con el valor actual de duración
				const nvaEta = calcularFechaMasDias($fecha_planificada.val(), $duracion_estimada_dias.val()) ?? '';
				$eta_calculada.val(nvaEta);
				if ($eta_calculada_hidden.length) $eta_calculada_hidden.val(nvaEta);

				// Deshabilita fechas de cabotaje
				$fechaEmbarque.prop('disabled', true).val('');
				$fechaArriboPuerto.prop('disabled', true).val('');
				$grupo_cabotaje.addClass('d-none');

				// Deshabilita selector de estado de rampla
				$estadoRampla.val('').prop('disabled', true).trigger('change.select2');
				$estadoRampla.select2({ placeholder: 'Seleccione estado de rampla' });

				// Deshabilita campos de rescate
				$fecha_rescate_puerto.prop('disabled', true).val('');
				$camion_rescate.val('').prop('disabled', true).trigger('change.select2');
				$camion_rescate.select2({ placeholder: '' });
				$conductor_rescate.val('').prop('disabled', true).trigger('change.select2');
				$conductor_rescate.select2({ placeholder: '' });

				$bloque_rescate.addClass('d-none');
				break;
		}

		// ⚠️ IMPORTANTE:
		// Solo recalcular estados de rampla cuando:
		// 1) El cambio proviene de una acción directa del usuario (listener real y NO inicializaciones de contexto).
		// 2) Estamos Editando la Planificación (aquí si se permite inicialización de contexto).
		const esEdicionPlanificacion = window.contextoVista?.esPlanificacion && window.contextoVista?.esEdit;
		// 1️⃣ Interacción real del usuario → solo si cambió
		if (window.listenerTipoTransporte && tipoTransporteId !== tipoTransporteIdActual) {
			estadosRamplaPorTipoTransporte($bloque, tipoTransporteId);
		}
		// 2️⃣ Edición de planificación → SIEMPRE inicializar
		if (esEdicionPlanificacion) {
			estadosRamplaPorTipoTransporte($bloque, tipoTransporteId);
		}

		window.listenerTipoTransporte = false;
	}
}
// Activar en cambios de campo tipo_transporte
$(document).on('change', '.tipo_transporte', function () {
	window.listenerTipoTransporte = true;
	toggleTipoTransporte($(this).closest('.retiro-region-xii'));
});

// Normalizacion de arreglos: conductores, camiones, ramplas y estados de ramplas.
// Se usa principalmente para encapsular en array de largo 1 cuando recibimos un valor escalar.
function normalizarArray(data, contexto = '') {
	if (Array.isArray(data)) {
		return data;
	}

	if (data === null || data === undefined) {
		console.warn(`⚠️ ${contexto}: data null/undefined, usando []`);
		return [];
	}

	if (typeof data === 'object') {
		console.warn(`⚠️ ${contexto}: data objeto, encapsulando en array`, data);
		return [data];
	}

	console.error(`❌ ${contexto}: data inválida`, data);
	return [];
}

// Carga conductores según empresa (transportista)
function cargarConductoresEmpresa($bloque, selectorBase, empresaId, conductorPreSeleccionadoId = null) {
    const $selectConductor = $bloque.find(selectorBase);

	if (!empresaId) {
        $selectConductor.html('<option value="">Seleccione Conductor</option>')
                        .val('')
                        .prop('disabled', true)
                        .trigger('change.select2');
        return;
    }

	$.get("/conductores/empresa/" + empresaId, function (data) {

        // 🛡️ Normalización defensiva
        const conductores = normalizarArray(
            data,
            'cargarConductoresEmpresa'
        );

        let opciones = '<option value="">Seleccione Conductor</option>';

		// const idxBloque = $bloque.closest('.retiro-item-wrapper').attr('data-index');
		// const idxRegion = $bloque.closest('.retiro-region').attr('data-region');
		// console.log('data-index    : ', idxBloque);
		// console.log('data-region   : ', idxRegion);
		// console.log('selectorBase  :', selectorBase);
		// console.log('empresaId     :', empresaId);
		// console.log('preSelecId    :', conductorPreSeleccionadoId);

        conductores.forEach(function (conductor) {
            const selected = (conductor.id == conductorPreSeleccionadoId) ? ' selected' : '';
            opciones += `<option value="${conductor.id}"${selected}>${conductor.nombre_completo}</option>`;
        });

		$selectConductor.html(opciones)
						.prop('disabled', false)
						.val(conductorPreSeleccionadoId)   // 👈 CLAVE
						.trigger('change');
	});
}
// Cambio en select de PATENTE de camión - CAMION RETIRO (Select2)
$(document).on('change', '.patente_camion', function () {

	// 🚫 Bloques inválidos: template o fuera de contexto
	const idxBloque = $(this).closest('.retiro-item-wrapper').attr('data-index');
    if (!idxBloque || idxBloque === '__INDEX__') {
        // console.log('⛔ Bloque ignorado (template o sin wrapper)', { idxBloque });
        return;
    }

    let $bloque = null;

	switch (window.regionOperativa) {
		case REGION_X:
			$bloque   = $(this).closest('.retiro-region-x');
			break;

		case REGION_XII:
			$bloque    = $(this).closest('.retiro-region-xii');
			break;

		default:	// Si no es X o XII no hace nada
			return;
	}

	const $currTransportista   = $bloque.find('.transportista');
	const $currTransportistaId = $bloque.find('.transportista_id');
	const $currTipoCamion      = $bloque.find('.tipo_camion');

	const $conductorActual     = $bloque.find('.conductor_actual');
    const conductorFromOld     = parseInt($conductorActual.val()) || null;

	const camionId = parseInt($(this).val()) || 0;
    if (camionId > 0) {
        $.get("/camiones/detalle/" + camionId, function (data) {
            const conductorPorDefectoId = parseInt(data.conductor?.id) || 0;
            const transportistaId       = parseInt(data.empresa?.id) || 0;
            const transportistaNombre   = data.empresa?.razon_social || '';
            const tipoCamionNombre      = data.tipo_camion?.nombre || '';

			$currTransportista.val(transportistaNombre);					// Restaura nombre del transportista
			$currTransportistaId.val(transportistaId);						// Restaura id del transportista
            $currTipoCamion.val(tipoCamionNombre);							// Restaura el tipo del camión

			// 🔑 Prioridad: rebote > default del camión

			const conductorIdFinal = conductorFromOld ?? conductorPorDefectoId ?? null;
			// console.log('conductorFromOld      :', conductorFromOld);
			// console.log('conductorPorDefectoId :', conductorPorDefectoId);
			// console.log('conductorIdFinal      :', conductorIdFinal);

            cargarConductoresEmpresa($bloque, '.conductor', transportistaId, conductorIdFinal);
        });

    } else {
        $currTransportista.val('');
        $currTransportistaId.val('');
        $currTipoCamion.val('');

		$conductor = $bloque.find('.conductor');
		$conductor.val('').prop('disabled', true).trigger('change.select2');
        $conductor.select2({ placeholder: '' });
    }
});
// Cambio en select de PATENTE de camión - CAMION RESCATE (Select2)
$(document).on('change', '.camion_rescate', function () {

	// 🚫 Bloques inválidos: template o fuera de contexto
	const idxBloque = $(this).closest('.retiro-item-wrapper').attr('data-index');
    if (!idxBloque || idxBloque === '__INDEX__') {
        // console.log('⛔ Bloque ignorado (template o sin wrapper)', { idxBloque });
        return;
    }

	$bloque = $(this).closest('.retiro-region-xii');

    const $conductorRescateActual = $bloque.find('.conductor_rescate_actual');
    const conductorFromOld = parseInt($conductorRescateActual.val()) || null;

	camionId   = parseInt($(this).val()) || 0;
	if (camionId > 0) {
		$.get("/camiones/detalle/" + camionId, function (data) {
			const conductorPorDefectoId = parseInt(data.conductor?.id) || 0;
			const transportistaId       = parseInt(data.empresa?.id) || 0;

			// 🔑 Prioridad: rebote > default del camión
			const conductorIdFinal = conductorFromOld ?? conductorPorDefectoId ?? null;

			// console.log('transportistaId       :', transportistaId);
			// console.log('conductorFromOld      :', conductorFromOld);
			// console.log('conductorPorDefectoId :', conductorPorDefectoId);
			// console.log('conductorIdFinal      :', conductorIdFinal);

			cargarConductoresEmpresa($bloque, '.conductor_rescate', transportistaId, conductorIdFinal);
		});

	} else {
		$conductor = $bloque.find('.conductor_rescate');
		$conductor.val('').prop('disabled', true).trigger('change.select2');
		$conductor.select2({ placeholder: '' });
	}
});

// Precarga de patente de rampla (Select2) al cargar la página
function ramplaPlanificacion($bloque) {
    const getRamplaId   = $bloque.find('.patente_rampla_actual').val();
    const getRamplaText = $bloque.find('.patente_rampla_texto').val();
    const $selectPatente = $bloque.find('.patente_rampla');

    // const idxBloque = $bloque.attr('data-index');
    // console.log('En ramplaPlanificacion - Procesando :', idxBloque);

    $.get("/ramplas", function (data) {

        // 🛡️ Normalización defensiva
        const listaRamplas = normalizarArray(data, 'ramplaPlanificacion');

		// 1) Limpiar y añadir opción VACÍA (sin texto)
        $selectPatente.empty().append(new Option('Seleccione una patente', '', true, false));

        // 2) Añadir el resto de opciones
        listaRamplas.forEach(rampla => {
            const selected = (getRamplaId == rampla.id);
            const option = new Option(rampla.patente, rampla.id, selected, selected);
            $selectPatente.append(option);
        });

        // 3) Precarga forzada si tenía valor significativo (no era 0 - NO ESPECIFICADO) y no vino en la lista (porque puede estar desactivo)
        if (parseInt(getRamplaId, 10) !== 0 && !$selectPatente.find(`option[value="${getRamplaId}"]`).length) {
            $selectPatente.append(new Option(getRamplaText, getRamplaId, true, true));
        }

        // 4) (Re)inicializar Select2 con placeholder + allowClear
        if ($selectPatente.hasClass("select2-hidden-accessible")) {
            $selectPatente.select2('destroy');
        }

        $selectPatente.select2({
            placeholder: $selectPatente.data('placeholder') || 'Seleccione una patente',
            allowClear: true,
            width: '100%'
        });

        // 5) Disparar change para que actualice la UI
        $selectPatente.trigger('change');
    });
}
// Rellenar select con estados de rampla válidos para un tipo de transporte (Tierra / Barcaza / Combinado).
function estadosRamplaPorTipoTransporte($bloque, tipoTransporteId) {
	const $select = $bloque.find('.estado_rampla');
	const $hidden = $bloque.find('.estado_rampla_actual');

	// console.log('estadosRamplaPorTipoTransporte> tipoTransporteId  :', tipoTransporteId);

	// Verificar que $select es un objeto jQuery válido y no está vacío
    if (!$select || !$select.jquery || !$select.length) {
		console.error('catalogo2select2Element: $select debe ser un objeto jQuery ó está vacío', $select);
        return $select;
    }

	$select.prop('disabled', false).trigger('change.select2');
	$select.select2({ placeholder: 'Seleccione estado de rampla' });

	const actual =  $hidden ? String($hidden.val() || '') : null; // actual : opción  pre-seleccionada - Asegurar tipo string

	$.get("/ramplas/estados-por-transporte/" + tipoTransporteId, function(data) {
		// 🛡️ Normalización defensiva
		const estados = normalizarArray(data, 'estadosRamplaPorTipoTransporte');

		// Construir opciones
		const options = [`<option value=""></option>`];
		estados.forEach(e => {
			const id = String(e.id);                    // Aseguramos tipo string
			const nombre = String(e.nombre);            // Aseguramos tipo string
			const selected = actual && id === actual ? ' selected' : '';
			options.push(`<option value="${id}"${selected}>${nombre}</option>`);
		});

		// Aplicar las opciones al select
		$select.html(options.join(''));
	})
	.fail(function (jqXHR, textStatus, errorThrown) {
		console.error(`estadosRamplaPorTipoTransporte: Error al cargar lista (${textStatus}):`, errorThrown);
		$select.html('<option value="">Error al cargar opciones</option>');
	});

	// Devolver el elemento para posible encadenamiento
	return $select;
}

// Precarga de patente de camión (Select2) por Región Operativa - al cargar la página
// En Región x selectorBase sólo será : '.patente_camion'
// En Región XII selectorBase será    : '.patente_camion' y '.camion_rescate'
function camionPlanificacionRegionOperativa($bloque, regionOperativa, selectorBase) {
    const getCamionId    = $bloque.find(selectorBase + '_actual').val();
    const getCamionText  = $bloque.find(selectorBase + '_texto').val();
    const $selectPatente = $bloque.find(selectorBase);

    // const idxBloque = $bloque.attr('data-index');
    // console.log('En camionPlanificacion - Procesando :', idxBloque);

	$.get("/camiones/region-operativa/" + regionOperativa, function (data) {

        // 🛡️ Normalización defensiva
        const camiones = normalizarArray(
            data,
            'camionPlanificacionRegionOperativa'
        );

        // 1) Limpiar y añadir opción VACÍA (sin texto)
        $selectPatente.empty().append(new Option('Seleccione una patente', '', true, false));

        // 2) Añadir el resto de opciones
        camiones.forEach(camion => {
            const selected = (getCamionId == camion.id);
            const option = new Option(camion.patente, camion.id, selected, selected);
            $selectPatente.append(option);
        });

        // 3) Precarga forzada si tenía valor significativo (no era 0 - NO ESPECIFICADO) y no vino en la lista (porque puede estar desactivo)
        if (parseInt(getCamionId, 10) !== 0 && !$selectPatente.find(`option[value="${getCamionId}"]`).length) {
            $selectPatente.append(new Option(getCamionText, getCamionId, true, true));
        }

        // 4) (Re)inicializar Select2 con placeholder + allowClear
        if ($selectPatente.hasClass("select2-hidden-accessible")) {
            $selectPatente.select2('destroy');
        }

        $selectPatente.select2({
            placeholder: $selectPatente.data('placeholder') || 'Seleccione una patente',
            allowClear: true,
            width: '100%'
        });

        // 5) Disparar change para que actualice la UI
        $selectPatente.trigger('change');
    });
}

// Esta inicialización la disparados desde 2 lugares
// 1) Al final de la carga - Para que los OLDs ya estén leidos
// 2) Cuando procesamos cambio de planta/sucursal
function inicializarPlanificacionRetiro() {
	// console.log('📦 En el bloque JS de DOMContentLoaded');
	// console.log('regionOperativa:', window.regionOperativa);

	// Aplicar el wrapper explícitamente al cargar
	aplicarRegionOperativa(window.regionOperativa);

	switch (window.regionOperativa) {
		case REGION_X:
			// Inicializar Select2 de tipo de materia y especie junto a la hora de llegada y camión seleccionado por cada retiro-item.
			// Incluimos el retiro-item-template para que los clones salgan listos
			$('.retiro-region-x').each(function () {												// Cambiamos $('[class^="retiro-item"]') por $('.retiro-region')
				const selectMateriaPrima = $(this).find('.tipo_materia_prima');						// ej: objeto jQuery tipo_materia_prima_0
				const hiddenMateriaPrima = $(this).find('.tipo_materia_prima_actual');				// ej: objeto jQuery tipo_materia_prima_actual_0
				const selectEspecie      = $(this).find('.especie');								// ej: objeto jQuery especie_0
				const hiddenEspecie      = $(this).find('.especie_actual');							// ej: objeto jQuery especie_actual_0

				// Usamos catalogo2select2Element para inmunizarnos de que en ambos contextos que repiten los IDs (Aquí vamos con Región X)
				catalogo2select2Element(CATEGORIA_TIPO_MATERIA_PRIMA, selectMateriaPrima, 'Seleccione tipo de materia prima', hiddenMateriaPrima);
				catalogo2select2Element(CATEGORIA_TIPO_ESPECIE, selectEspecie, 'Seleccione una especie', hiddenEspecie);

				calcularHoraLlegada($(this));
				camionPlanificacionRegionOperativa($(this), REGION_X,   '.patente_camion');			// Para camión retiro Plantsa X Región y traslado a Planta La Portada
			});

			// Activar selector visual de hora y minuto con Flatpickr en modo 24h y sin calendario.
			// Así no escriben nada manual y desaparece el AM/PM completamente.
			flatpickr(".timepicker", {
				enableTime: true,
				noCalendar: true,
				dateFormat: "H:i",   // formato 24h HH:mm
				time_24hr: true,     // sin AM/PM
				minuteIncrement: 1,  // salto de 1 minuto
				disableMobile: true  // fuerza Flatpickr en móviles
			});

			break;

		case REGION_XII:
			$('.retiro-region-xii').each(function () {												// Cambiamos $('[class^="retiro-item"]') por $('.retiro-region')
				const selectMateriaPrima = $(this).find('.tipo_materia_prima');						// ej: objeto jQuery tipo_materia_prima_0
				const hiddenMateriaPrima = $(this).find('.tipo_materia_prima_actual');				// ej: objeto jQuery tipo_materia_prima_actual_0
				const selectEspecie      = $(this).find('.especie');								// ej: objeto jQuery especie_0
				const hiddenEspecie      = $(this).find('.especie_actual');							// ej: objeto jQuery especie_actual_0

				// Usamos catalogo2select2Element para inmunizarnos de que en ambos contextos que repiten los IDs (Aquí vamos con Región XII)
				catalogo2select2Element(CATEGORIA_TIPO_MATERIA_PRIMA, selectMateriaPrima, 'Seleccione tipo de materia prima', hiddenMateriaPrima);
				catalogo2select2Element(CATEGORIA_TIPO_ESPECIE, selectEspecie, 'Seleccione una especie', hiddenEspecie);

				calcularHoraLlegada($(this));

				const selectTipoTransporte = $(this).find('.tipo_transporte');						// ej: objeto jQuery tipo_transporte_0
				const hiddenTipoTransporte = $(this).find('.tipo_transporte_actual');				// ej: objeto jQuery tipo_transporte_actual_0
				catalogo2select2Element(CATEGORIA_TIPO_TRANSPORTE, selectTipoTransporte, 'Seleccione tipo de transporte', hiddenTipoTransporte);

				ramplaPlanificacion($(this));

				// toggleTipoTransporte($(this));													// Se optimizan llamadas repetidas. Ya se llama desde dentro de aplicarRegionOperativa()

				camionPlanificacionRegionOperativa($(this), REGION_XII, '.patente_camion');			// Para camión retiro en Planta Region XII
				camionPlanificacionRegionOperativa($(this), REGION_X, '.camion_rescate');			// Para camión rescate en puerto Puerto Montt
			});

			break;

		default:	// Si no es X o XII no hace nada
			break;
	}
}
document.addEventListener('DOMContentLoaded', inicializarPlanificacionRetiro);
