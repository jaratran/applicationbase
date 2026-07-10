<!-- Extrajimos las Acciones de Planificación para ponerlas en ambos costados de la dataTable -->

    <div class="d-grid gap-1">
        <div class="d-flex justify-content-center gap-1">
            <!-- SHOW hace la busqueda por Retiro más sus datos de Planificación -->
            <button class="btn btn-info btn-xs text-white btnShowPlanificacion" type="button" data-id="{{ $retiro->id }}" data-reg-operativa-id="{{ $retiro->planificacion?->region_operativa_id  }}">
                <i class="fa fa-eye"></i>
            </button>

            <!-- Primero evaluamos si corresponde desplegar botón al rol del usuario -->
            @if( in_array(Auth::user()->rol_id, [ config('constantes.ROL_COORDINADOR'), config('constantes.ROL_COORDINADOR_XII'), config('constantes.ROL_ADMINISTRADOR_IT') ]) )

                <!-- Botón de EDITAR solo si el retiro está en estado Aceptado, Planificado y/o Programado -->
                @if (	$retiro->estado_id == config('constantes.ESTADO_RETIRO_ACEPTADO') ||
                        $retiro->estado_id == config('constantes.ESTADO_RETIRO_PLANIFICADO') ||
                        $retiro->estado_id == config('constantes.ESTADO_RETIRO_PROGRAMADO') )

                    <!-- EDIT recupera información por Retiro más sus datos de Planificación -->
                    <!-- Pero después el UPDATE recibe id de planificación y actualiza directo en Planificación por Planificación -->
                    <a	class="btn btn-warning btn-xs text-white"
                        href="{{ route('planificaciones-retiro.edit', ['id' => Crypt::encrypt($retiro->id)]) }}">

                        <!-- Muestra un ícono distinto si es la primera vez: Planificando -->
                        @if ($retiro->estado_id == config('constantes.ESTADO_RETIRO_ACEPTADO'))
                            <i class="fa fa-calendar-alt text-primary"></i>
                        @else
                            <i class="fa fa-edit"></i>
                        @endif

                    </a>
                @endif
            @endif
        </div>

        <div class="d-flex justify-content-center gap-1">
            <!-- Primero evaluamos si corresponde desplegar botón al rol del usuario -->
            @if( in_array(Auth::user()->rol_id, [ config('constantes.ROL_COORDINADOR'), config('constantes.ROL_COORDINADOR_XII'), config('constantes.ROL_ADMINISTRADOR_IT') ]) )

                <!-- Botón de CIERRE de Planificación solo si el retiro está en estado Programado -->
                @if ( $retiro->estado_id == config('constantes.ESTADO_RETIRO_PROGRAMADO') )

                    <!-- Cierre de Planificación hace la busqueda directa por Planificación en Planificación -->
                    <button class="btn btn-success btn-xs btnCierrePlanificacion" type="button" data-id="{{ $retiro->planificacion?->id ?? 0 }}">
                        <i class="fa fa-check-circle"></i>
                    </button>
                @endif

                <!-- Botón de ANULAR Planificación solo si el retiro NO está en estado Terminado ni Cancelado -->
                @if (	$retiro->estado_id != config('constantes.ESTADO_RETIRO_TERMINADO') &&
                        $retiro->estado_id != config('constantes.ESTADO_RETIRO_CANCELADO') )

                    <!-- DELETE de Planificación hace la busqueda directa por Planificación en Planificación -->
                    <button class="btn btn-danger btn-xs btnDeletePlanificacion" type="button" data-id="{{ $retiro->planificacion?->id ?? 0 }}">
                        <i class="fa fa-trash"></i>
                    </button>
                @endif
            @endif
        </div>
    </div>
