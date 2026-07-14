@php
    $esPlantilla       = ($index  === '__INDEX__');                                   // Control para saber si estamos en plantilla
    $hoy               = \Carbon\Carbon::now();                                       // Fecha de hoy para definir los límites permitidos del retiro
@endphp

{{-- Si estamos en la vista de creación el titulo va acompañado el indice porque podrá no ser el único --}}
@if ($esCreate)
    <h5 class="titulo-retiro">Datos del Retiro #{{ $esPlantilla ? 'Retiro Plantilla' : ($index + 1) }}</h5>
@else
    <h5 class="titulo-retiro">Datos del Retiro</h5>
@endif

{{-- Bloque del detalle de la solicitud del retiro --}}
<div class="detalle-retiro">

	<!--
		<div class="row my-3 py-3">
			<div class="form-group col-md-12">
				data-debug-index          = "{{ $index }}"
				data-debug-plantilla      = "{{  $esPlantilla ? 'si' : 'no' }}"

				data-debug-tipo-retiro    = "{{  $esPlantilla ? '' : (old("tipo_retiro.$index", optional($retiro)->tipo_retiro_id) ?? '') }}"
				data-debug-es-bins        = "{{ ($esPlantilla ? '' : (old("tipo_retiro.$index", optional($retiro)->tipo_retiro_id) ?? '')) == config('constantes.TIPO_RETIRO_BINS') ? 'si' : 'no' }}"

				data-debug-checkbox-value = "{{  $esPlantilla ? '' : (old("requiere_reposicion_hidden.$index") ?? optional($retiro)->requiere_reposicion ?? '') }}"
				data-debug-original       = "{{  $esPlantilla ? '' : (optional($retiro)->requiere_reposicion ?? '') }}">

				<label for="requiere_reposicion_{{ $index }}" style="position: absolute; top: 0;">
					¿Requiere reposición de Bins?
					{{-- Muestra valores en pantalla (solo desarrollo) --}}
					@if(env('APP_DEBUG'))
						<small class="text-muted d-block">
							[DEBUG: Índice={{ $index }},
							TipoRetiro={{ $esPlantilla ? '' : (old("tipo_retiro.$index", optional($retiro)->tipo_retiro_id) ?? '') }},
							Valor={{ $esPlantilla ? '' : (old("requiere_reposicion_hidden.$index") ?? optional($retiro)->requiere_reposicion ?? '') }}]
						</small>
					@endif
				</label>
			</div>
		</div>
	-->

    <div class="row">
        <div class="form-group col-md-4">
            <label for="fecha_retiro_{{ $index }}">Fecha y horario de Retiro</label>

            @php
                $minDateTimeRetiro = $hoy->copy()->startOfDay()->subMonth()->format('Y-m-d\TH:i');      // Borde inferior de fecha para solicitar : 00:00 de la fecha un mes atras.
                $maxDateTimeRetiro = $hoy->copy()->endOfDay()->addYear()->format('Y-m-d\TH:i');         // Borde superior de fecha para solicitar : 23:59 de la fecha un año adelante.
            @endphp

            <input type="datetime-local"
                min="{{ $minDateTimeRetiro }}"                  {{-- Borde inferior del retiro: un mes atrás. --}}
                max="{{ $maxDateTimeRetiro }}"                  {{-- Borde superior del retiro: un año adelante. --}}
				name="fecha_retiro[]"

                id="fecha_retiro_{{ $index }}"
                class="form-control fecha_retiro"
                {{-- value="{{ $esPlantilla ? '' : old("fecha_retiro.$index", optional($retiro)->fecha_retiro) }}" --}}
                {{-- data-original="{{ $esPlantilla ? '' : (optional($retiro)->fecha_retiro ?? '') }}" --}}

                value="{{ $esPlantilla ? '' : old("fecha_retiro.$index", optional($retiro?->fecha_retiro)->format('Y-m-d\TH:i')) }}"
                data-original="{{ $esPlantilla ? '' : optional($retiro?->fecha_retiro)->format('Y-m-d\TH:i') }}"

                {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
        </div>

        <div class="form-group col-md-4">
            <label for="tipo_retiro_{{ $index }}">Tipo de retiro</label>

            <select name="tipo_retiro[]"
                    id="tipo_retiro_{{ $index }}"
                    class="form-control select2 tipo_retiro"

					data-original="{{ $esPlantilla ? '' : (old("tipo_retiro.$index", optional($retiro)->tipo_retiro_id) ?? '') }}"
                    data-modificado="false"

                    {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
                    <option value="">Cargando Tipos de Retiro...</option>
            </select>

            <input type="hidden" class="tipo_retiro_actual" id="tipo_retiro_actual_{{ $index }}" value="{{ $esPlantilla ? '' : old("tipo_retiro.$index", optional($retiro)->tipo_retiro_id) }}">

            @if (!$esPlantilla && !$esCreate)
                <input type="hidden" class="tipo_retiro_original" id="tipo_retiro_original_{{ $index }}" value="{{ optional($retiro)->tipo_retiro_id ?? '' }}">
            @endif
        </div>

        <div class="form-group col-md-4">
            <label for="kilogramos_estimados_{{ $index }}">Kilogramos estimados</label>
            <input type="number"
				name="kilogramos_estimados[]"
                id="kilogramos_estimados_{{ $index }}"
                class="form-control kilogramos_estimados"
                min="0"
                step="1"
                value="{{ $esPlantilla ? '' : old("kilogramos_estimados.$index", optional($retiro)->kilogramos_estimados ?? 0 ) }}"

				data-original="{{ $esPlantilla ? '' : (optional($retiro)->kilogramos_estimados ?? 0) }}"

                {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
        </div>
    </div>

    <div class="row my-3 grupo-reposicion">
		<div class="form-group col-md-4 position-relative mt-1">
			<label for="requiere_reposicion_{{ $index }}" style="position: absolute; top: 0;">
                ¿Requiere reposición de Bins?
            </label>
			<div class="form-switch ms-3" style="margin-top: 1.8rem;">
				<input type="hidden"
					class="form-control requiere_reposicion_hidden"
					name="requiere_reposicion_hidden[]"
					id="requiere_reposicion_hidden_{{ $index }}"
					value="{{ $esPlantilla ? '' : (old("requiere_reposicion_hidden.$index") ?? optional($retiro)->requiere_reposicion ?? 0) }}">

                @if (($esPlantilla ? '' : (old("tipo_retiro.$index", optional($retiro)->tipo_retiro_id) ?? '')) == config('constantes.TIPO_RETIRO_BINS'))
					<input type="checkbox"
						class="form-check-input requiere_reposicion"
						name="requiere_reposicion[]"
						id="requiere_reposicion_{{ $index }}"

						{{ ($esPlantilla ? '' : (old("requiere_reposicion_hidden.$index") ?? optional($retiro)->requiere_reposicion ?? '')) == '1' ? 'checked' : '' }}

						value="1"
						data-original="{{ $esPlantilla ? '' : (optional($retiro)->requiere_reposicion == '1' ? '1' : '0') }}"
						style="transform: scale(2);"

						>
				@else
                    <input type="checkbox"
                        class="form-check-input requiere_reposicion"
						name="requiere_reposicion[]"
                        id="requiere_reposicion_{{ $index }}"

						{{ ($esPlantilla ? '' : (old("requiere_reposicion_hidden.$index") ?? optional($retiro)->requiere_reposicion ?? '')) == '1' ? 'checked' : '' }}

                        value="1"
                        data-original="{{ $esPlantilla ? '' : (optional($retiro)->requiere_reposicion == '1' ? '1' : '0') }}"
                        style="transform: scale(2);"

                        {{ $esPlantilla ? '' : 'disabled' }}> {{-- No es tipo_retiro BINs por lo tanto si no es plantilla debe estar disabled --}}
                @endif
            </div>
        </div>

		<div class="form-group col-md-4">
			<label for="cantidad_bins_{{ $index }}">Cantidad de Bins a reponer</label>

			<input type="hidden"
				class="form-control cantidad_bins_hidden"
				name="cantidad_bins_hidden[]"
				id="cantidad_bins_hidden_{{ $index }}"
				value="{{ $esPlantilla ? '' : (old("cantidad_bins_hidden.$index") ?? optional($retiro)->cantidad_bins ?? '') }}">

			@if ( $esPlantilla ? '' : (old("requiere_reposicion_hidden.$index") ?? optional($retiro)->requiere_reposicion ?? false))
				<input type="number"
					class="form-control cantidad_bins"
					name="cantidad_bins[]"
					id="cantidad_bins_{{ $index }}"
					value="{{ $esPlantilla ? '' : (old("cantidad_bins_hidden.$index") ?? optional($retiro)->cantidad_bins ?? '') }}"

					data-original="{{ $esPlantilla ? '' : (optional($retiro)->cantidad_bins ?? '') }}"
					min="0"

					{{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
			@else
				<input type="number"
					class="form-control cantidad_bins"
					name="cantidad_bins[]"
					id="cantidad_bins_{{ $index }}"
					value="{{ $esPlantilla ? '' : (old("cantidad_bins_hidden.$index") ?? optional($retiro)->cantidad_bins ?? '') }}"

					data-original="{{ $esPlantilla ? '' : (optional($retiro)->cantidad_bins ?? '') }}"
					min="0"

					{{ $esPlantilla ? '' : 'disabled' }}>
			@endif
		</div>
    </div>

    {{-- Si estamos en la vista de creación debemos mostrar el boton de eliminar retiro en caso de que no sea el único --}}
    @if ($esCreate)
        <div class="text-end">
            <button type="button" class="btn btn-danger btn-sm btnRemoveRetiro {{ $esPlantilla ? '' : ($loop->first ? 'd-none' : '') }}">
                <i class="fa fa-trash"></i> Eliminar Retiro
            </button>
        </div>
    @endif

</div>
