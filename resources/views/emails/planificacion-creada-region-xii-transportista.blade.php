<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planificación de Retiro - Region XII - del Día {{ \Carbon\Carbon::parse($planificacion->eta_calculada)->format('d-m-Y') }} / {{ $empresa->razon_social }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <p>Estimados <strong>{{ $empresa->razon_social }}</strong>,</p>

							@switch($planificacion->tipo_operacion)
								@case(config('constantes.TIPO_OPERACION_RETIRO'))
									<p>se ha planificado el retiro de materia prima en la Región XII para su flota de camiones con el siguiente detalle:</p>
									@break

								@case(config('constantes.TIPO_OPERACION_REPOSICION'))
									<p>se ha planificado la reposición de bines en la Región XII para su flota de camiones con el siguiente detalle:</p>
									@break

								@default
									-
							@endswitch

                            <table width="100%" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-size: 13px; margin-top: 20px;">
                                <thead style="background-color: #f0f0f0;">
                                    <tr>
                                        <th>N° Retiro</th>
                                        <th>Fecha y Hora Agendada</th>
                                        <th>Patente Camión</th>
                                        <th>Patente Rampla</th>
                                        <th>Tipo Camión</th>
                                        <th>Nombre Chofer</th>
                                        <th>RUT Chofer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#{{ $planificacion->retiro->id }}</td>
										<td>{{ \Carbon\Carbon::parse($planificacion->fecha_hora_planificada)->format('d-m-Y H:i') }}</td>

										@if ( $planificacion->tipo_operacion == config('constantes.TIPO_OPERACION_REPOSICION') )
											<td>—</td>
											<td>—</td>
											<td>—</td>
											<td>—</td>
											<td>—</td>
										@else
											<td>{{ $planificacion->camion->patente ?? '—' }}</td>
											<td>{{ $planificacion->patente_rampla ?? '—' }}</td>
											<td>{{ $planificacion->camion->tipoCamion->nombre ?? '—' }}</td>
											<td>{{ $planificacion->conductor->nombre }} {{ $planificacion->conductor->apellido }}</td>
											<td>{{ $planificacion->conductor->rut }}</td>
										@endif

                                    </tr>
                                </tbody>
                            </table>

                            <p style="margin-top: 30px;">
                                Ante cualquier duda contactar al Coordinador de Retiros de Materia Prima de La Portada.
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
