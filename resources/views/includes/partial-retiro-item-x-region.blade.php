@php
    $esPlantilla       = ($index  === '__INDEX__');                                   // Control para saber si estamos en plantilla
    $hoy               = \Carbon\Carbon::now();                                       // Fecha de HOY para pivotear limites inferior y superior de las fechas de retiro y planificación
@endphp

{{-- Si estamos en la vista de creación el titulo va acompañado el indice porque podrá no ser el único --}}
@if ($esCreate)
    <h5 class="titulo-retiro">Datos del Retiro #{{ $esPlantilla ? 'Retiro Plantilla' : ($index + 1) }}</h5>
@else
    <h5 class="titulo-retiro">Datos del Retiro</h5>
@endif

{{-- Bloque del detalle de la solicitud del retiro --}}
<div class="detalle-retiro {{ $esPlanificacion ? 'bg-light border rounded p-3 mb-4' : '' }}">
    @if ($esPlanificacion)
        <h5>Detalle del Retiro</h5>
    @endif

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
                min="{{ $minDateTimeRetiro }}"                  {{-- Borde inferior en el ingreso de fechas : retiro y planificación. Un mes atras. --}}
                max="{{ $maxDateTimeRetiro }}"                  {{-- Borde superior en el ingreso de fechas : retiro y planificación. Un año adelante. --}}
				name="fecha_retiro[]"

                id="fecha_retiro_{{ $index }}"
                class="form-control fecha_retiro"
                {{-- value="{{ $esPlantilla ? '' : old("fecha_retiro.$index", optional($retiro)->fecha_retiro) }}" --}}
                {{-- data-original="{{ $esPlantilla ? '' : (optional($retiro)->fecha_retiro ?? '') }}" --}}

                value="{{ $esPlantilla ? '' : old("fecha_retiro.$index", optional($retiro?->fecha_retiro)->format('Y-m-d\TH:i')) }}"
                data-original="{{ $esPlantilla ? '' : optional($retiro?->fecha_retiro)->format('Y-m-d\TH:i') }}"

                {{ $esPlantilla ? '' : ($esCreate ? 'required' : ($esPlanificacion ? 'disabled' : '' )) }}>
        </div>

        <div class="form-group col-md-4">
            <label for="tipo_retiro_{{ $index }}">Tipo de retiro</label>

            <select name="tipo_retiro[]"
                    id="tipo_retiro_{{ $index }}"
                    class="form-control select2 tipo_retiro"

					data-original="{{ $esPlantilla ? '' : (old("tipo_retiro.$index", optional($retiro)->tipo_retiro_id) ?? '') }}"
                    data-modificado="false"

                    {{ $esPlantilla ? '' : ($esCreate ? 'required' : ($esPlanificacion ? 'disabled' : '' )) }}>
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

                {{ $esPlantilla ? '' : ($esCreate ? 'required' : ($esPlanificacion ? 'disabled' : '' )) }}>
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

						{{ $esPlantilla ? '' : ($esCreate ? '' : ($esPlanificacion ? 'disabled' : '' )) }}>
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

					{{ $esPlantilla ? '' : ($esCreate ? 'required' : ($esPlanificacion ? 'disabled' : '' )) }}>
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

    {{-- Si estamos en la vista de edición de las Planificaciones corresponde mostrar el historial de comentarios --}}
    @if ($esEdit && $esPlanificacion)
        @if ($retiro->comentarios->isNotEmpty())
            <div class="row my-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Historial de Comentarios</h5>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#comentariosCollapse"
                            aria-expanded="false" aria-controls="comentariosCollapse" id="toggleComentariosBtn">
                            <i class="fa fa-chevron-down me-1" id="iconToggleComentarios"></i>
                            <span id="textToggleComentarios">Mostrar</span>
                    </button>
                </div>

                <div class="collapse" id="comentariosCollapse">
                    <div class="border rounded p-3" style="max-height: 280px; overflow-y: auto;">
                        @foreach ($retiro->comentarios as $comentario)
                            <div class="comentario mb-2">
                                <small class="text-muted">[{{ $comentario->created_at_format }}] {{ $comentario->usuario->nombre_usuario }} {{ $comentario->usuario->apellidos_usuario }}:</small>
                                <p class="mb-0">{{ $comentario->comentario }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

@if ($esPlanificacion)
    {{-- Bloque del detalle de la planificación del retiro --}}
    <div class="detalle-planificacion bg-light border rounded p-3">
        <h5>Detalle de la Planificación</h5>

        <div class="row">
            <div class="form-group col-md-4">
                <label for="fecha_planificada_{{ $index }}">Fecha y horario planificado</label>
                @if (isset($retiro))
                    {{-- Por si nos invocamos desde la creacón MANUAL donde no hay retiro --}}
                    {{-- Primero validamos que exista retiro (con isset) y luego si está en estado ACEPTADO --}}
                    @switch($retiro->estado_id)
                        @case(config('constantes.ESTADO_RETIRO_ACEPTADO')) {{-- Si está en estado ACEPTADO es un retiro generado por SOLICITANTE y aceptado por COORDINADOR --}}
                            @php
                                // Tomamos la fecha solicitada para el retiro (si viene nula usamos "ahora" como fallback)
                                $fechaSol = optional($retiro?->fecha_retiro) ?? $hoy;
                                $fechaMin = $fechaSol->max($hoy);                                               // Escojemos lo mayor entre hoy y la fecha solicitada para el retiro (usando Carbon::max).

                                $minDateTimePlan = $fechaMin->copy()->startOfDay()->format('Y-m-d\TH:i');       // Borde inferior de fecha para planificar : 00:00 de la fecha mayor entre hoy y la fecha solicitada para el retiro.
                                $maxDateTimePlan = $hoy->copy()->endOfDay()->addYear()->format('Y-m-d\TH:i');   // Borde superior de fecha para planificar : 23:59 de la fecha un año adelante.
                            @endphp

                            <input type="datetime-local"
                                min="{{ $minDateTimePlan }}"               {{-- Borde inferior de fecha para planificar : lo mayor entre hoy y la fecha solicitada para el retiro. --}}
                                max="{{ $maxDateTimePlan }}"               {{-- Borde superior de fecha para planificar : Un año adelante. --}}
								name="fecha_planificada[]"

                                id="fecha_planificada_{{ $index }}"
                                class="form-control fecha_planificada"
                                value="{{ $esPlantilla ? '' : old("fecha_planificada.$index", optional($retiro?->fecha_retiro)->format('Y-m-d\TH:i')) }}"

                                data-original="{{ $esPlantilla ? '' : optional($retiro?->fecha_retiro)->format('Y-m-d\TH:i') }}"
                                data-traza="CASE ACEPTADO"

                                {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
                            @break

                        @case(config('constantes.ESTADO_RETIRO_PROGRAMADO')) {{-- Si está en estado PROGRAMADO no debe poder modificar la fecha --}}
                            @php
                                // Tomamos la fecha planificada existente (si viene nula usamos "ahora" como fallback)
                                $fechaPlan = optional($retiro?->planificacion?->fecha_hora_planificada) ?? $hoy;

                                // Definimos límites en base a la fecha para cuando esta PROGRAMADO el retiro
                                $minDateTimePlan = $fechaPlan->copy()->startOfDay()->format('Y-m-d\TH:i'); // 00:00 de la fecha previamente programada
                                $maxDateTimePlan = $fechaPlan->copy()->endOfDay()->format('Y-m-d\TH:i');   // 23:59 de la fecha previamente programada
                            @endphp

                            <input type="datetime-local"
                                min="{{ $minDateTimePlan }}"               {{-- Borde inferior de fecha para re-programar : 00:00 de la fecha previamente programada. --}}
                                max="{{ $maxDateTimePlan }}"               {{-- Borde superior de fecha para re-programar : 23:59 de la fecha previamente programada. --}}
								name="fecha_planificada[]"

                                id="fecha_planificada_{{ $index }}"
                                class="form-control fecha_planificada"
                                value="{{ $esPlantilla ? '' : old("fecha_planificada.$index", optional($retiro?->planificacion?->fecha_hora_planificada)->format('Y-m-d\TH:i')) }}"

                                data-original="{{ $esPlantilla ? '' : optional($retiro?->planificacion?->fecha_hora_planificada)->format('Y-m-d\TH:i') }}"
                                data-traza="CASE PROGRAMADO"

                                {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
                            @break

                        @default                                                {{-- Si está NO en estado ACEPTADO ni PROGRAMADO debe estár en estado PLANIFICADO --}}
                            @php
                                // Tomamos la fecha solicitada para el retiro (si viene nula usamos "ahora" como fallback)
                                $fechaSol = optional($retiro?->fecha_retiro) ?? $hoy;
                                $fechaMin = $fechaSol->max($hoy);                                               // Escojemos lo mayor entre hoy y la fecha solicitada para el retiro (usando Carbon::max).

                                $minDateTimePlan = $fechaMin->copy()->startOfDay()->format('Y-m-d\TH:i');       // Borde inferior de fecha para planificar : 00:00 de la fecha mayor entre hoy y la fecha solicitada para el retiro.
                                $maxDateTimePlan = $hoy->copy()->endOfDay()->addYear()->format('Y-m-d\TH:i');   // Borde superior de fecha para planificar : 23:59 de la fecha un año adelante.
                            @endphp

                            <input type="datetime-local"
                                min="{{ $minDateTimePlan }}"               {{-- Borde inferior de fecha para re-planificar : lo mayor entre hoy y la fecha solicitada para el retiro. --}}
                                max="{{ $maxDateTimePlan }}"               {{-- Borde superior de fecha para re-planificar : Un año adelante. --}}
								name="fecha_planificada[]"

                                id="fecha_planificada_{{ $index }}"
                                class="form-control fecha_planificada"
                                value="{{ $esPlantilla ? '' : old("fecha_planificada.$index", optional($retiro?->planificacion?->fecha_hora_planificada)->format('Y-m-d\TH:i')) }}"

                                data-original="{{ $esPlantilla ? '' : optional($retiro?->planificacion?->fecha_hora_planificada)->format('Y-m-d\TH:i') }}"
                                data-traza="DEFAULT - PLANIFICADO"

                                {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
                    @endswitch

                @else                               {{-- Si  NO existe registro de retiro es porque estamos en una CREACIÓN MANUAL de coordinador o  Admin-IT --}}
                    @php
                        // Tomamos la fecha que se está solicitando para el retiro (si está nula usamos "ahora" como fallback)
                        $fechaSol = $retiro?->fecha_retiro ?? $hoy;
                        $fechaMin = $fechaSol->max($hoy);                                // Escojemos lo mayor entre hoy y la fecha que se está solicitando para el retiro (usando Carbon::max).

                        $minDateTimePlan = $fechaMin->copy()->startOfDay()->format('Y-m-d\TH:i');       // Borde inferior de fecha para planificar : 00:00 de la fecha mayor entre hoy y la fecha que se está solicitando para el retiro.
                        $maxDateTimePlan = $hoy->copy()->endOfDay()->addYear()->format('Y-m-d\TH:i');   // Borde superior de fecha para planificar : 23:59 de la fecha un año adelante.
                    @endphp

                    <input type="datetime-local"
                        min="{{ $minDateTimePlan }}"                        {{-- Borde inferior de fecha para planificar : Hoy. --}}
                        max="{{ $maxDateTimePlan }}"                        {{-- Borde superior de fecha para planificar : Un año adelante. --}}
						name="fecha_planificada[]"

                        id="fecha_planificada_{{ $index }}"
                        class="form-control fecha_planificada"
                        value="{{ $esPlantilla ? '' : old("fecha_planificada.$index", optional($retiro?->planificacion?->fecha_hora_planificada)->format('Y-m-d\TH:i')) }}"

                        data-original="{{ $esPlantilla ? '' : optional($retiro?->planificacion?->fecha_hora_planificada)->format('Y-m-d\TH:i') }}"
                        data-traza="DELOCONTRARIO - CREACION MANUAL"

                        {{ $esPlantilla ? '' : (($esCreate || $esEdit)? 'required' : '') }}>
                @endif
            </div>

            <div class="form-group col-md-4">
                <label for="duracion_viaje_{{ $index }}">Horas de duración del viaje a Planta la Portada</label>

                {{-- Clase Flatpickr: selector visual de hora y minuto en modo 24h y sin calendario. --}}
                <input type="text"
					name="duracion_viaje[]"
                    id="duracion_viaje_{{ $index }}"
                    class="form-control duracion_viaje timepicker"
                    value="{{ $esPlantilla ? '00:00' : old("duracion_viaje.$index", optional($retiro?->planificacion)?->duracion_viaje ? \Carbon\Carbon::parse($retiro->planificacion->duracion_viaje)->format('H:i') : '00:00') }}"

                    data-original="{{ $esPlantilla ? '00:00' : (optional($retiro?->planificacion)?->duracion_viaje ? \Carbon\Carbon::parse($retiro->planificacion->duracion_viaje)->format('H:i') : '00:00') }}"

                    {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
            </div>

            <div class="form-group col-md-4">
                <label for="hora_llegada_{{ $index }}">Fecha y hora estimada de llegada</label>

                <input type="datetime-local"
					name="hora_llegada[]"
                    id="hora_llegada_{{ $index }}"
                    class="form-control hora_llegada"
                    value="{{ $esPlantilla ? '' : old("hora_llegada_estimada.$index", optional($retiro?->planificacion?->hora_llegada_estimada)?->format('Y-m-d\TH:i')) }}"
                    data-original="{{ $esPlantilla ? '' : (optional($retiro?->planificacion)?->hora_llegada_estimada?->format('Y-m-d\TH:i') ?? '') }}"
                    disabled>

                <input type="hidden"
					name="hora_llegada_estimada_hidden[]"
                    id="hora_llegada_estimada_hidden_{{ $index }}"
                    class="form-control hora_llegada_estimada_hidden"
                    value="{{ $esPlantilla ? '' : (old("hora_llegada_estimada_hidden.$index") ?? optional($retiro?->planificacion)?->hora_llegada_estimada?->format('Y-m-d\TH:i') ?? '') }}">
            </div>
        </div>

        <div class="row my-3">
            <div class="form-group col-md-4">
                <label for="tipo_materia_prima_{{ $index }}">Tipo de Materia Prima</label>

                <select name="tipo_materia_prima[]"
                        id="tipo_materia_prima_{{ $index }}"
                        class="form-control select2 tipo_materia_prima"

                        data-original="{{ $esPlantilla ? '' : (optional($retiro?->planificacion)?->tipo_materia_prima_id ?? '') }}"
                        data-modificado="false"

                        {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
                    <option value="">Cargando Tipos de Materia Prima...</option>
                </select>

                <input type="hidden"
                    class="tipo_materia_prima_actual"
                    id="tipo_materia_prima_actual_{{ $index }}"
                    value="{{ $esPlantilla ? '' : old("tipo_materia_prima.$index", optional($retiro?->planificacion)?->tipo_materia_prima_id) }}">

                @if (!$esPlantilla && !$esCreate)
                    <input type="hidden"
                        class="tipo_materia_prima_original"
                        id="tipo_materia_prima_original_{{ $index }}"
                        value="{{ optional($retiro?->planificacion)?->tipo_materia_prima_id ?? '' }}">
                @endif
            </div>

            <div class="form-group col-md-4">
                <label for="especie_{{ $index }}">Especie</label>

                <select name="especie[]"
                        id="especie_{{ $index }}"
                        class="form-control select2 especie"

                        data-original="{{ $esPlantilla ? '' : (optional($retiro?->planificacion)?->especie_id ?? '') }}"
                        data-modificado="false"

                        {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
                    <option value="">Cargando especies ...</option>
                </select>

                <input type="hidden"
                    class="especie_actual"
                    id="especie_actual_{{ $index }}"
                    value="{{ $esPlantilla ? '' : old("especie.$index", optional($retiro?->planificacion)?->especie_id) }}">

                @if (!$esPlantilla && !$esCreate)
                    <input type="hidden"
                        class="especie_original"
                        id="especie_original_{{ $index }}"
                        value="{{ optional($retiro?->planificacion)?->especie_id ?? '' }}">
                @endif
            </div>

            <div class="form-group col-md-4 position-relative mt-1">
                <label for="tiene_restriccion_{{ $index }}" style="position: absolute; top: 0;">
                    ¿Existe alguna restricción?
                </label>
                <div class="form-switch ms-3" style="margin-top: 1.8rem;">
                    <input type="hidden"
                        class="form-control tiene_restriccion_hidden"
                        name="tiene_restriccion_hidden[]"
                        id="tiene_restriccion_hidden_{{ $index }}"
                        value="{{ $esPlantilla ? '' : (old("tiene_restriccion_hidden.$index") ?? optional($retiro?->planificacion)?->tiene_restriccion ?? 0) }}">

                    <input type="checkbox"
                        class="form-check-input tiene_restriccion"
                        name="tiene_restriccion[]"
                        id="tiene_restriccion_{{ $index }}"
                        {{ ($esPlantilla ? '' : (old("tiene_restriccion_hidden.$index") ?? optional($retiro?->planificacion)?->tiene_restriccion ?? '')) == '1' ? 'checked' : '' }}
                        value="1"

                        data-original="{{ $esPlantilla ? '' : (optional($retiro?->planificacion)?->tiene_restriccion == '1' ? '1' : '0') }}"

                        style="transform: scale(2);">
                </div>
            </div>
        </div>

        <div class="row my-3">
            <div class="form-group col-md-4">
                <label for="patente_camion_{{ $index }}">Patente de Camión</label>

                <select name="patente_camion[]"
                        id="patente_camion_{{ $index }}"
                        class="form-control select2 patente_camion"

                        data-original="{{ $esPlantilla ? '' : (optional($retiro?->planificacion)?->camion?->id ?? '') }}"
                        data-modificado="false"
                        data-placeholder="Seleccione una patente"

                        {{ $esPlantilla ? '' : ($esCreate ? 'required' : '') }}>
                    <option value="">Cargando Patentes ...</option>
                </select>

                <input type="hidden"
                    class="form-control patente_camion_actual"
                    id="patente_camion_actual_{{ $index }}"
                    value="{{ $esPlantilla ? '' : old("patente_camion.$index", optional($retiro?->planificacion)?->camion?->id) }}">

                <input type="hidden"
                    class="form-control patente_camion_texto"
                    id="patente_camion_texto_{{ $index }}"
                    value="{{ $esPlantilla ? '' : old("patente_camion_texto.$index", optional($retiro?->planificacion)?->camion?->patente) }}">

                @if (!$esPlantilla && !$esCreate)
                    <input type="hidden"
                        class="form-control patente_camion_original"
                        id="patente_camion_original_{{ $index }}"
                        value="{{ optional($retiro?->planificacion)?->camion?->id ?? '' }}">
                @endif
            </div>

            <div class="form-group col-md-4">
                <label for="patente_rampla_{{ $index }}">Patente de Rampla</label>

                <input type="text"
					name="patente_rampla[]"
					id="patente_rampla_{{ $index }}"
                    class="form-control patente_rampla"
                    placeholder="Ej: AB-1234"

                    value="{{ $esPlantilla ? '' : old("patente_rampla.$index", optional($retiro?->planificacion)?->patente_rampla) }}"

                    data-original="{{ $esPlantilla ? '' : (optional($retiro?->planificacion)?->patente_rampla ?? '') }}">
            </div>
        </div>

        <div class="row my-3">
            <div class="form-group col-md-4">
                <label for="transportista_{{ $index }}">Transportista</label>

                <input type="text"
					name="transportista[]"
                    id="transportista_{{ $index }}"
                    class="form-control transportista"
                    value="{{ $esPlantilla ? '' : old("transportista.$index", optional($retiro?->planificacion?->camion?->empresa)->razon_social) }}"
                    data-original="{{ $esPlantilla ? '' : (optional($retiro?->planificacion?->camion?->empresa)->razon_social ?? '') }}"
                    disabled>

                <!-- Esto es para usarlo en el JS de pre-carga del conductor -->
                <input type="hidden"
                    class="form-control transportista_id"
                    id="transportista_id_{{ $index }}"
                    value="{{ $esPlantilla ? '' : old("transportista_id.$index", optional($retiro?->planificacion?->camion)->empresa_id) }}">
            </div>

            <div class="form-group col-md-4">
                <label for="tipo_camion_{{ $index }}">Tipo de Camión</label>

                <input type="text"
                    name="tipo_camion[]"
                    id="tipo_camion_{{ $index }}"
                    class="form-control tipo_camion"
                    value="{{ $esPlantilla ? '' : old("tipo_camion.$index", optional($retiro?->planificacion?->camion?->tipoCamion)->nombre) }}"

                    data-original="{{ $esPlantilla ? '' : (optional($retiro?->planificacion?->camion?->tipoCamion)->nombre ?? '') }}"

                    disabled>
            </div>

            <div class="form-group col-md-4">
                <label for="conductor_{{ $index }}">Conductor</label>

                {{-- Si no estamos en Plantilla --}}
                @if (! $esPlantilla)
                    @php
                        // Si hay rebote, usamos el valor viejo
                        $idConductor = old("conductor.$index");

                        // Si no hay rebote y hay conductor asignado (≠ 0), lo usamos
                        if (is_null($idConductor) && optional($retiro?->planificacion)->conductor_id > 0) {
                            $idConductor     = optional($retiro?->planificacion)->conductor_id;
                            $nombreConductor = optional($retiro?->planificacion?->conductor)->nombre_completo ?? '';
                        }

                        // Si sigue sin haber conductor y el camión tiene uno por defecto (≠ 0)
                        if (is_null($idConductor) && optional($retiro?->planificacion?->camion)->conductor_id > 0) {
                            $idConductor     = optional($retiro?->planificacion?->camion)->conductor_id;
                            $nombreConductor = optional($retiro?->planificacion?->camion?->conductor)->nombre_completo ?? '';
                        }

                        // Si no hay nada, dejamos todo en blanco
                        $idConductor     ??= '';
                        $nombreConductor ??= '';
                    @endphp
                @endif

                <select name="conductor[]"
                        id="conductor_{{ $index }}"
                        class="form-control select2 conductor"

                        data-original="{{ $esPlantilla ? '' : $idConductor }}"
                        data-modificado="false"

                        data-placeholder="Seleccione Conductor..."
						{{ $esPlantilla ? '' : 'disabled' }}> {{-- Este campo se habilita por JS cuando se selecciona un camión --}}

                    <option value="">Cargando Conductores ...</option>
                </select>

                <input type="hidden" class="conductor_actual" id="conductor_actual_{{ $index }}" value="{{ $esPlantilla ? '' : $idConductor }}">
                <input type="hidden" class="conductor_texto" id="conductor_texto_{{ $index }}" value="{{ $esPlantilla ? '' : $nombreConductor }}">

                @if (!$esPlantilla && !$esCreate)
                    <input type="hidden" class="conductor_original" id="conductor_original_{{ $index }}" value="{{ optional($retiro?->planificacion)->conductor_id }}">
                @endif
            </div>
        </div>
    </div>
@endif
