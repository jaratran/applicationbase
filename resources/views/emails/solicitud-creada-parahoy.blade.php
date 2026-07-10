<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Retiro para Hoy</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <p>Estimado/a Coordinador/a,</p>

                            <p>Se ha ingresado una solicitud de retiro de materia prima y/o de reposicion de bines que contiene una o más operaciones requeridas para el día de hoy. Esta situación requiere su atención inmediata para asegurar una correcta coordinación operativa.</p>

                            <p>¿Qué puede hacer ahora?</p>
                            <ul>
                                <li>Revisar la solicitud y validar los retiros asociados que tienen ejecución requerida para hoy.</li>
                                <li>Coordinar con transporte, planta y otras áreas involucradas según corresponda.</li>
                                <li>Utilizar el sistema para comentar, aprobar o anular retiros si fuese necesario.</li>
                            </ul>

                            <p>Puedes acceder directo a la solicitud y revisar antecedentes a través del siguiente botón:</p>
                            <p>
                                <a href="{{ $urlDestino }}" style="display: inline-block; padding: 10px 20px; background-color: {{ $designParameter->custom_primary }}; color: white; text-decoration: none; border-radius: 5px;">
                                    Revisar solicitud para hoy
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
