<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo comentario del Coordinador</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <p>Estimado/a <strong>{{ $data['usuario']->nombre_usuario }} {{ $data['usuario']->apellidos_usuario }}</strong>,</p>

                            <p>Hemos revisado su solicitud de Retiro de Materia Prima y/o Reposición de Bines  N°{{ $data['retiroId'] }} la que fue comentada por el coordinador y necesitamos su revisión y/o ajuste.</p>

                            <p>¿Qué puedes hacer ahora?</p>
                            <ul>
                                <li>Revisar el comentario ingresado por el coordinador.</li>
                                <li>Realizar las modificaciones que consideres necesarias.</li>
                                <li>Agregar una respuesta o contacto si se requiere.</li>
                            </ul>

                            <p>Puedes acceder directo al retiro/reposición solicitada y responder a través del siguiente botón:</p>
                            <p>
                                <a href="{{ $urlDestino }}" style="display: inline-block; padding: 10px 20px; background-color: {{ $designParameter->custom_primary }}; color: white; text-decoration: none; border-radius: 5px;">
                                    Ver comentario y solicitud
                                </a>
                            </p>

                            <p style="margin-top: 40px;">Atentamente,<br><strong>EcoRuta<br>Logística La Portada</strong></p>

                            <hr>
                            <p class="mb-0"><small>Este correo fue generado automáticamente por el Sistema de Planificación EcoRuta.</small></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
