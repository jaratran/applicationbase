<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cancelación de Retiro Programado – {{ $empresa->razon_social }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <h2 style="color: {{ $designParameter->custom_primary }};">
                                Cancelación de Retiro Programado
                            </h2>

                            <p>Estimados <strong>{{ $empresa->razon_social }}</strong>,</p>

							@switch($planificacion->tipo_operacion)
								@case(config('constantes.TIPO_OPERACION_RETIRO'))
		                            <p>Les informamos que se ha <strong>cancelado la planificación</strong> de un retiro que involucraba uno de sus camiones.</p>
									@break

								@case(config('constantes.TIPO_OPERACION_REPOSICION'))
		                            <p>Les informamos que se ha <strong>cancelado la planificación</strong> de una reposición de bines que involucraba uno de sus camiones.</p>
									@break

								@default
									-
							@endswitch

                            <h4 style="margin-bottom: 10px;">📋 Detalle del Retiro Cancelado:</h4>
                            <table width="100%" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                                <thead style="background-color: #f0f0f0;">
                                    <tr>
                                        <th>N° Retiro</th>
                                        <th>Fecha y Hora Agendada</th>

										<th>Patente Camión</th>
                                        <th>Patente Rampla</th>
                                        <th>Tipo Camión</th>
                                        <th>Nombre Chofer</th>
                                        <th>Rut Chofer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $datos['numero_retiro'] ?? '—' }}</td>
                                        <td>{{ $datos['fecha_hora_agendada'] ?? '—' }}</td>

										@if ( $planificacion->tipo_operacion == config('constantes.TIPO_OPERACION_REPOSICION') )
											<td>—</td>
											<td>—</td>
											<td>—</td>
											<td>—</td>
											<td>—</td>
										@else
											<td>{{ $datos['patente_camion'] ?? '—' }}</td>
											<td>{{ $datos['patente_rampla'] ?? '—' }}</td>
											<td>{{ $datos['tipo_camion'] ?? '—' }}</td>
											<td>{{ $datos['nombre_chofer'] ?? '—' }}</td>
											<td>{{ $datos['rut_chofer'] ?? '—' }}</td>
										@endif
                                    </tr>
                                </tbody>
                            </table>

                            <p style="margin-top: 30px;">
                                Ante cualquier duda contactar al Coordinador de Retiro de Materia Prima de La Portada.
                            </p>

                            <p style="margin-top: 40px;">Atentamente,<br><strong>Sistema de Planificación de Retiro de Materia Prima</strong></p>

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
