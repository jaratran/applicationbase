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

			{{-- Input visible (solo informativo) --}}
			<input type="text" class="form-control tipo_retiro_display" value="Bins" disabled>

			{{-- Valor real que se envía al backend --}}
			<input type="hidden"
				name="tipo_retiro[]"
				id="tipo_retiro_{{ $index }}"
				class="tipo_retiro"

				data-original="{{ config('constantes.TIPO_RETIRO_BINS') }}"
				data-modificado="false"

				value="{{ config('constantes.TIPO_RETIRO_BINS') }}">

			{{-- Mantiene compatibilidad con JS existentes --}}
			<input type="hidden" class="tipo_retiro_actual" id="tipo_retiro_actual_{{ $index }}" value="{{ old("tipo_retiro.$index", config('constantes.TIPO_RETIRO_BINS')) }}">

			@if (!$esPlantilla && !$esCreate)
				<input type="hidden" class="tipo_retiro_original" id="tipo_retiro_original_{{ $index }}" value="{{ config('constantes.TIPO_RETIRO_BINS') }}">
			@endif
		</div>

        <div class="form-group col-md-4">
            <label for="kilogramos_estimados_{{ $index }}">Kilogramos estimados</label>

            <input type="hidden"
                class="form-control kilogramos_estimados_hidden"
                name="kilogramos_estimados_hidden[]"
                id="kilogramos_estimados_hidden_{{ $index }}"
                value="{{ $esPlantilla ? '' : (old("kilogramos_estimados_hidden.$index") ?? optional($retiro)->kilogramos_estimados ?? '') }}">

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

				<input type="checkbox"
					class="form-check-input requiere_reposicion"
					name="requiere_reposicion[]"
					id="requiere_reposicion_{{ $index }}"

					{{ ($esPlantilla ? '' : (old("requiere_reposicion_hidden.$index") ?? optional($retiro)->requiere_reposicion ?? '')) == '1' ? 'checked' : '' }}

					value="1"
					data-original="{{ $esPlantilla ? '' : (optional($retiro)->requiere_reposicion == '1' ? '1' : '0') }}"
					style="transform: scale(2);"

					>
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

		<div class="form-group col-md-4 position-relative mt-1">
			<label for="tipo_operacion_{{ $index }}" style="position: absolute; top: 0;">
				Tipo de solicitud
			</label>

			<div class="d-flex align-items-center" style="margin-top: 1.8rem;">

				<!-- Etiqueta izquierda -->
				<span class="text-muted small">Retiro</span>

				<div class="form-switch ms-4 me-3">
					<input type="hidden"
						class="form-control tipo_operacion_hidden"
						name="tipo_operacion_hidden[]"
						id="tipo_operacion_hidden_{{ $index }}"
						value="{{ $esPlantilla ? '' : (old("tipo_operacion_hidden.$index") ?? optional($retiro)->tipo_operacion ?? 0) }}">

					<input type="checkbox"
						class="form-check-input tipo_operacion"
						name="tipo_operacion[]"
						id="tipo_operacion_{{ $index }}"

						{{ ($esPlantilla ? '' : (old("tipo_operacion_hidden.$index") ?? optional($retiro)->tipo_operacion ?? '')) == '1' ? 'checked' : '' }}

						value="1"
						data-original="{{ $esPlantilla ? '' : (optional($retiro)->tipo_operacion == '1' ? '1' : '0') }}"
						style="transform: scale(2);"

						>
				</div>

				<!-- Etiqueta derecha -->
				<span class="text-muted small">Reposición</span>

			</div>
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
