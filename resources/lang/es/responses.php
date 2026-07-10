<?php

return [

    'solicitudes' => [
        'store_success'             => 'La solicitud fue creada exitosamente.',
        'store_error'               => 'Ocurrió un error al guardar la solicitud. Intenta nuevamente.',

        'update_success'            => 'El retiro fue actualizado correctamente.',
        'update_error'              => 'Ocurrió un error al actualizar el retiro. Intenta nuevamente.',

        'delete_success'            => 'El retiro fue cancelado correctamente.',
        'delete_error'              => 'Ocurrió un error al intentar cancelar el retiro. Por favor, inténtalo nuevamente o contacta al administrador.',

        'comentario_guardado'       => 'Comentario guardado correctamente.',
        'comentario_error'          => 'Ocurrió un error al guardar el comentario. Por favor, intente nuevamente.',

        'retiro_aprobado'           => 'El retiro ha sido aprobado exitosamente.',
        'retiro_aprobado_error'     => 'No se pudo aprobar el retiro. Por favor, intente nuevamente o contacte al administrador.',

        'store_manual_success'      => 'La creación manual de la solicitud fue exitosa.',
        'store_manual_error'        => 'Ocurrió un error al guardar la creación manual de la solicitud. Intenta nuevamente.',
    ],

    'parametros' => [
        'update_success'            => 'Parámetros actualizados correctamente.',
        'no_changes_detected'       => 'No hubo cambios que guardar.',
        'update_error'              => 'Ocurrió un error al actualizar los parámetros. Por favor, inténtelo nuevamente.',
    ],

    'planificacion' => [
        'update_success'            => 'Planificación actualizada correctamente.',
        'update_failed'             => 'Ocurrió un error al intentar actualizar la información. Intente nuevamente.',

        'cierre_success'            => 'Planificación cerrada correctamente.',
        'cierre_error'              => 'Ocurrió un error al intentar cerrar la planificación. Intente nuevamente.',

        'delete_success'            => 'La planificación ha sido anulada correctamente.',
        'delete_error'              => 'Ocurrió un error al intentar anular la planificación.',
    ],

    'telegram' => [
        'pin_generado'              => 'PIN generado, tiene una validez de :minutos minutos. Entregue este código al conductor: /vincular :pin',
        'desvinculado_exito'        => 'Vinculación de Telegram eliminada correctamente.',
        'desvinculado_error'        => 'No se pudo desvincular al conductor. Intente nuevamente.',     
        
        'vinculacion_exitosa'       => '✅ ¡Hola :nombre! Tu cuenta ha sido vinculada correctamente. Ahora recibirás notificaciones por este medio.',
        'desvinculacion_admin'      => '🚫 Hola :nombre, tu cuenta ha sido desvinculada de LaPortada por un administrador. Si esto fue un error, comunícate con tu coordinador.',
        'pin_invalido'              => '⚠️ El código ingresado no es válido o ha expirado. Por favor, solicite un nuevo código con el coordinador.',
        'error_vinculacion'         => '❌ Ocurrió un error al intentar completar la vinculación. Contactese con el coordinador.',        
    ],

    'programa' => [
        'previsualizacion_fecha_invalida' => 'La fecha proporcionada para previsualización no es válida.',
        'previsualizacion_error'          => 'Error inesperado al cargar datos del programa diario.',

        'emitido_exito'                   => 'Programa Diario emitido exitosamente para el :fecha (Version :version).',
        'fecha_invalida'                  => 'La fecha del programa diario no es válida.',
        'sin_detalle'                     => 'No se recibió la información de detalle del programa.',
        'detalle_invalido'                => 'El formato del detalle del programa es inválido.',
        'error_emision'                   => 'Ocurrió un error al emitir el Programa Diario. Intente nuevamente.',

        'fecha_invalida'                  => 'La fecha seleccionada no tiene un formato válido.',
        'sin_versiones_en_fecha'          => 'No hay versiones emitidas para la fecha :fecha.',
    ],

    // Aquí puedes ir agrupando por módulo o tipo
    // 'usuarios' => [
    //     'update_success' => 'El usuario fue actualizado correctamente.',
    //     'destroy_error'  => 'No fue posible eliminar el usuario.',
    // ],
];
