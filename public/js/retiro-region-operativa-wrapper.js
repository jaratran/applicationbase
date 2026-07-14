/*
 * Activa el bloque de Retiro correspondiente a la región operativa seleccionada.
 * El parcial mantiene ambos contextos y solo el activo participa en el envío.
 */
function aplicarRegionOperativa(regionOperativa) {
    // Procesa los retiros existentes, pero omite el wrapper usado como plantilla para nuevos ítems.
    $('.retiro-item-wrapper').filter(function () {
        return $(this).closest('.retiro-item-template').length === 0;
    }).each(function () {
        const $wrapper = $(this);
        let $retiroContexto = null;

        switch (regionOperativa) {
            case REGION_X:
                // La región inactiva queda oculta y sus campos no participan en validación ni envío.
                $wrapper.find('.retiro-region-xii')
                    .addClass('d-none')
                    .find('input, select, textarea')
                    .prop('disabled', true)
                    .removeAttr('required');

                $retiroContexto = $wrapper.find('.retiro-region-x');
                $retiroContexto.removeClass('d-none');
                $retiroContexto.find('.fecha_retiro, .tipo_retiro, .kilogramos_estimados').prop('disabled', false);
                $retiroContexto.find('.fecha_retiro, .kilogramos_estimados').prop('required', true);
                // Los campos ocultos conservan los valores normalizados que espera el backend.
                $retiroContexto.find('.requiere_reposicion_hidden, .cantidad_bins_hidden').prop('disabled', false);
                break;

            case REGION_XII:
                // La región inactiva queda oculta y sus campos no participan en validación ni envío.
                $wrapper.find('.retiro-region-x')
                    .addClass('d-none')
                    .find('input, select, textarea')
                    .prop('disabled', true)
                    .removeAttr('required');

                $retiroContexto = $wrapper.find('.retiro-region-xii');
                $retiroContexto.removeClass('d-none');

                // En Región XII, una operación de reposición no requiere informar kilogramos estimados.
                const esReposicion = $retiroContexto.find('.tipo_operacion').prop('checked');
                $retiroContexto.find('.fecha_retiro, .tipo_retiro').prop('disabled', false);
                $retiroContexto.find('.fecha_retiro').prop('required', true);
                $retiroContexto.find('.kilogramos_estimados').prop('disabled', esReposicion);
                $retiroContexto.find('.kilogramos_estimados_hidden, .requiere_reposicion_hidden, .cantidad_bins_hidden, .tipo_operacion, .tipo_operacion_hidden').prop('disabled', false);
                break;

            default:
                console.warn('aplicarRegionOperativa> región operativa indefinida; se omite.', regionOperativa);
                return;
        }

        if ($retiroContexto.length) {
            // Aplica las reglas del tipo de retiro, que pueden modificar el estado de reposición.
            toggleTipoRetiro($retiroContexto);

            // Reejecuta el evento para sincronizar los campos dependientes con el estado resultante.
            const $requiere = $retiroContexto.find('.requiere_reposicion');
            if ($requiere.length) {
                $requiere.trigger('change');
            }

            // Actualiza la cantidad de bins después de completar la sincronización del contexto activo.
            toggleCantidadBins($retiroContexto);
        }
    });
}
