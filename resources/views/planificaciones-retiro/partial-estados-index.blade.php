<!-- Extrajimos los Estados de Planificación para ponerlos en ambos costados de la dataTable -->

    @switch($retiro->estado_id)
        @case(config('constantes.ESTADO_RETIRO_ESPERANDO'))
            <i class="fas fa-clock text-warning fs-4" title="Esperando"></i>
            <div>Esperando</div>
            @break
        @case(config('constantes.ESTADO_RETIRO_COMENTADO'))
            <i class="fas fa-comment-dots text-info fs-4" title="Comentada"></i>
            <div>Comentado</div>
            @break
        @case(config('constantes.ESTADO_RETIRO_ACEPTADO'))
            <i class="fas fa-check-circle text-success fs-4" title="Aceptada"></i>
            <div>Aceptado</div>
            @break
        @case(config('constantes.ESTADO_RETIRO_PLANIFICADO'))
            <i class="fas fa-calendar-check text-primary fs-4" title="Planificada"></i>
            <div>Planificado</div>
            @break
        @case(config('constantes.ESTADO_RETIRO_PROGRAMADO'))
            <i class="fas fa-route text-primary fs-4" title="Programado"></i>
            <div>Programado</div>
            @break
        @case(config('constantes.ESTADO_RETIRO_TERMINADO'))
            <i class="fas fa-flag-checkered text-secondary fs-4" title="Terminada"></i>
            <div>Terminado</div>
            @break
        @case(config('constantes.ESTADO_RETIRO_CANCELADO'))
            <i class="fas fa-times-circle text-danger fs-4" title="Cancelada"></i>
            <div>Cancelado</div>
            @break
        @default
            <i class="fas fa-question-circle text-danger fs-4" title="No Disponible"></i>
            <div>No Disponible</div>
    @endswitch
