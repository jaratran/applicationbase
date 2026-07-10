<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Retiro Creada</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <p>Estimado/a <strong>{{ $usuario->nombre_usuario }} {{ $usuario->apellidos_usuario }}</strong>,</p>

							<p>Hemos recibido correctamente su solicitud de Retiro(s) de Materia Prima y/o Reposición de Bines, registrado(s) según los datos de a continuación.</p>

                            <h4 style="margin-bottom: 10px;">🗂 Detalle de Retiros Asociados:</h4>
                            <table width="100%" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                                <thead style="background-color: #f0f0f0;">
                                    <tr>
                                        <th>N° Retiro</th>
                                        <th>Fecha Hora Solicitud</th>
                                        <th>Proveedor</th>
                                        <th>Lugar de Retiro</th>
                                        <th>Fecha Hora Retiro</th>
                                        <th>Tipo Retiro</th>
                                        <th>Kg Estimados</th>
                                        <th>¿Reposición?</th>
                                        <th>Cant. Bins</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($solicitudes as $solicitud)
                                        @foreach ($solicitud->retiros as $retiro)
                                            <tr>
                                                <td>#{{ $retiro->id }}</td>
                                                <td>{{ \Carbon\Carbon::parse($solicitud->created_at)->format('d-m-Y H:i') }}</td>
												<td>{{ $solicitud->maquila->empresa->razon_social ?? '—' }}</td>
												<td>{{ $solicitud->maquila->sucursal->nombre_sucursal ?? '—' }}</td>
												<td>{{ \Carbon\Carbon::parse($retiro->fecha_retiro)->format('d-m-Y H:i') }}</td>

												@if ( $retiro->tipo_operacion == config('constantes.TIPO_OPERACION_REPOSICION') )
													<td>—</td>
													<td>—</td>
												@else
													<td>{{ $retiro->tipoRetiro->nombre ?? '—' }}</td>
													<td>{{ number_format($retiro->kilogramos_estimados, 0, ',', '.') }} kg</td>
												@endif

												<td>{{ $retiro->requiere_reposicion ? 'Sí' : 'No' }}</td>
												<td>{{ $retiro->cantidad_bins ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>

                            <hr style="margin: 40px 0; border: none; border-top: 1px solid #ccc;">

                            <p>¿Qué sucede ahora?</p>
                            <ul>
                                <li>Un coordinador revisará los retiros asociados y validará la información ingresada.</li>
                                <li>Le notificaremos en caso de tener comentarios o si su solicitud fue planificada.</li>
                                <li>Podrás seguir el estado de la solicitud desde el sistema en cualquier momento.</li>
                                <li>Si es necesario, podrás realizar comentarios o cancelar la solicitud.</li>
                            </ul>

                            <p>Puedes acceder directamente al sistema para revisar el detalle de tu solicitud a través del siguiente botón:</p>
                            <p>
                                <a href="{{ $urlDestino }}" style="display: inline-block; padding: 10px 20px; background-color: {{ $designParameter->custom_primary }}; color: white; text-decoration: none; border-radius: 5px;">
                                    Ver mi solicitud de retiro
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
