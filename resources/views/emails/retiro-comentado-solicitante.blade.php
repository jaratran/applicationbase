<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo comentario del Solicitante</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <p>Estimado/a Coordinador(a),</p>

                            <p>El solicitante ha respondido el comentario que usted realizó al retiro o reposición solicitado N°{{ $data['retiroId'] }} del {{ $data['fechaRetiro'] }}.</p>

                            <p>¿Qué debes hacer ahora?</p>
                            <ul>
                                <li>Revisar el comentario ingresado por el solicitante.</li>
                                <li>Considerarlo en tu proceso de evaluación o aprobación.</li>
                                <li>Responder o dejar nuevas observaciones si es necesario.</li>
                            </ul>

                            <p>Puedes acceder directo al retiro solicitado y revisar antecedentes a través del siguiente botón:</p>
                            <p>
                                <a href="{{ $urlDestino }}" style="display: inline-block; padding: 10px 20px; background-color: {{ $designParameter->custom_primary }}; color: white; text-decoration: none; border-radius: 5px;">
                                    Ver solicitud comentada
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
