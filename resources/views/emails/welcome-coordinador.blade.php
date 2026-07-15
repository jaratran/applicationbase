<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido al Sistema de Planificación</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <p>Estimado/a <strong>{{ $user->nombre_usuario }} {{ $user->apellidos_usuario }}</strong>,</p>

                            <h2 style="color: {{ $designParameter->custom_primary }};">
                                ¡Bienvenido/a a <strong>EcoRuta</strong> el Sistema de Planificación de Retiro de Subproductos de <strong>La Portada</strong>!
                            </h2>

                            <p>Nos complace informarte que tu acceso ha sido habilitado exitosamente.</p>

                            <p><strong>¿Qué puedes hacer en el sistema?</strong></p>
                            <ul>
								<li>Consultar el historial y estado de las solicitudes de retiro.</li>
								<li>Interactuar con el solicitante mediante comentarios sobre una solicitud.</li>
								<li>Validar solicitudes de retiro realizadas.</li>
								<li>Registrar y planificar solicitudes de retiro que no fueron ingresadas por solicitantes en el sistema. Permitiendo contar con todos los retiros planificados en el sistema.</li>
								<li>Ajustar las solicitudes o cancelarlas si es necesario.</li>
								<li>Visualizar programas históricos (Planificado y Real).</li>
								<li>Visualizar Dashboard con indicadores.</li>
                            </ul>

                            <p><strong>Accede al sistema aquí:</strong></p>
                            <p>
                                <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 10px 20px; background-color: {{ $designParameter->custom_primary }}; color: white; text-decoration: none; border-radius: 5px;">
                                    Activar cuenta y definir contraseña
                                </a>
                            </p>

                            <p><strong>Tu usuario es tu correo: </strong>{{ $user->email }}</p>

                            <p>Si tienes alguna duda o necesitas asistencia, no dudes en comunicarte con el coordinador de la plataforma al correo <strong>{{ $operationalParameter->support_email }}</strong> o al teléfono <strong>{{ $operationalParameter->support_telefono }}</strong>.
                            
                            <p style="margin-top: 40px;">Saludos cordiales,<br><strong>Equipo de Logística de La Portada</strong></p>

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
