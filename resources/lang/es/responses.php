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

    ],

    'parametros' => [
        'update_success'            => 'Parámetros actualizados correctamente.',
        'no_changes_detected'       => 'No hubo cambios que guardar.',
        'update_error'              => 'Ocurrió un error al actualizar los parámetros. Por favor, inténtelo nuevamente.',
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

    // Aquí puedes ir agrupando por módulo o tipo
    // 'usuarios' => [
    //     'update_success' => 'El usuario fue actualizado correctamente.',
    //     'destroy_error'  => 'No fue posible eliminar el usuario.',
    // ],
];
