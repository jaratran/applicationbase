<?php

namespace App\Helpers;

class RenderEstadoNovedad
{
    /**
     * Retorna HTML con ícono y texto para un estado de retiro
     *
     * @param int|null $estado
     * @return string HTML
     */
    public static function renderEstadoIcono($estado): string
    {
        switch ($estado) {
            case config('constantes.ESTADO_RETIRO_PROGRAMADO'):
                return '<i class="fas fa-hourglass-half text-warning fs-4" title="En Proceso"></i><div>En Proceso</div>';

            case config('constantes.ESTADO_RETIRO_TERMINADO'):
                return '<i class="fas fa-check-circle text-success fs-4" title="Efectuada"></i><div>Efectuada</div>';

            case config('constantes.ESTADO_RETIRO_CANCELADO'):
                return '<i class="fas fa-times-circle text-danger fs-4" title="Cancelada"></i><div>Cancelada</div>';

            default:
                return '<i class="fas fa-question-circle text-danger fs-4" title="No Disponible"></i><div>No Disponible</div>';
        }
    }

    /**
     * Retorna HTML con ícono y texto para una novedad de retiro
     *
     * @param int|null $novedad
     * @return string HTML
     */
    public static function renderNovedadIcono($novedad): string
    {
        switch ($novedad) {
            case config('constantes.CALIDAD_RETIRO_ACTUALIZADO'):
                return '<i class="fas fa-sync-alt text-info fs-4" title="Actualizada"></i><div>Actualizada</div>';

            case config('constantes.CALIDAD_RETIRO_NUEVO'):
                return '<i class="fas fa-plus-circle text-primary fs-4" title="Nueva"></i><div>Nueva</div>';
                
            default:
                return ''; // No mostramos nada si es ORIGINAL u otro valor no visualizable
        }
    }
}
