<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PanelController extends Controller
{
    /**
     * Muestra el panel demostrativo de ApplicationBase.
     *
     * Todos los datos de esta vista son simulados y no consultan fuentes externas.
     */
    public function index(): View
    {
        $indicadores = [
            ['titulo' => 'Usuarios activos', 'valor' => '1.248', 'variacion' => '+8,2%', 'icono' => 'fa-users', 'color' => 'primary'],
            ['titulo' => 'Procesos completados', 'valor' => '864', 'variacion' => '+12,5%', 'icono' => 'fa-check-circle', 'color' => 'success'],
            ['titulo' => 'Tareas pendientes', 'valor' => '37', 'variacion' => '-4,1%', 'icono' => 'fa-clock', 'color' => 'warning'],
            ['titulo' => 'Tasa de cumplimiento', 'valor' => '94,6%', 'variacion' => '+2,3%', 'icono' => 'fa-chart-line', 'color' => 'info'],
        ];

        $actividadMensual = [
            'etiquetas' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            'valores' => [120, 155, 142, 190, 214, 238],
        ];

        $distribucionEstados = [
            'etiquetas' => ['Completado', 'En curso', 'Pendiente'],
            'valores' => [62, 25, 13],
        ];

        $ultimasActividades = [
            ['fecha' => '12-07-2026', 'actividad' => 'Actualización de información general', 'usuario' => 'Andrea Soto', 'estado' => 'Completado'],
            ['fecha' => '12-07-2026', 'actividad' => 'Revisión de solicitud interna', 'usuario' => 'Martín Rojas', 'estado' => 'En curso'],
            ['fecha' => '11-07-2026', 'actividad' => 'Validación de antecedentes', 'usuario' => 'Camila Díaz', 'estado' => 'Pendiente'],
            ['fecha' => '10-07-2026', 'actividad' => 'Cierre de proceso de prueba', 'usuario' => 'Diego Silva', 'estado' => 'Completado'],
            ['fecha' => '09-07-2026', 'actividad' => 'Registro de nueva actividad', 'usuario' => 'Paula Reyes', 'estado' => 'En curso'],
            ['fecha' => '08-07-2026', 'actividad' => 'Asignación de responsables', 'usuario' => 'Felipe Torres', 'estado' => 'Completado'],
            ['fecha' => '07-07-2026', 'actividad' => 'Preparación de resumen semanal', 'usuario' => 'Valentina Mora', 'estado' => 'Pendiente'],
            ['fecha' => '06-07-2026', 'actividad' => 'Confirmación de datos ingresados', 'usuario' => 'Javier Castro', 'estado' => 'Completado'],
            ['fecha' => '05-07-2026', 'actividad' => 'Seguimiento de tarea interna', 'usuario' => 'Sofía Núñez', 'estado' => 'En curso'],
            ['fecha' => '04-07-2026', 'actividad' => 'Publicación de reporte general', 'usuario' => 'Tomás Vidal', 'estado' => 'Completado'],
            ['fecha' => '03-07-2026', 'actividad' => 'Control de información pendiente', 'usuario' => 'Daniela Pérez', 'estado' => 'Pendiente'],
            ['fecha' => '02-07-2026', 'actividad' => 'Actualización de estado de proceso', 'usuario' => 'Nicolás Fuentes', 'estado' => 'En curso'],
        ];

        return view('panel', compact(
            'indicadores',
            'actividadMensual',
            'distribucionEstados',
            'ultimasActividades'
        ));
    }
}
