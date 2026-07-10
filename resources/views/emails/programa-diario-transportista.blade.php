<!DOCTYPE html>
@php
    use App\Helpers\RenderEstadoNovedad;
@endphp

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Programación de Camiones del Día {{ \Carbon\Carbon::parse($programaDiario->fecha_programa)->format('d-m-Y') }} / {{ $empresa->razon_social }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <p>Estimados <strong>{{ $empresa->razon_social }}</strong>,</p>

                            <p>A continuación, enviamos el listado de retiros planificados para su flota de camiones:</p>

                            <h4 style="margin-bottom: 10px;">📋 Detalle de Retiros Programados:</h4>
                            <table width="100%" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                                <thead style="background-color: #f0f0f0;">
                                    <tr>
                                        <th>Estado</th>
                                        <th>Novedad</th>
                                        <th>Patente Camión</th>
                                        <th>Fecha y Hora Retiro Agendado</th>
                                        <th>Patente Rampla</th>
                                        <th>Tipo Camión</th>
                                        <th>Nombre Chofer</th>
                                        <th>Rut Chofer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datos as $fila)
                                        <tr>
                                            <td class="text-center align-middle">
                                                {!! RenderEstadoNovedad::renderEstadoIcono($fila['estado']) !!}
                                            </td>
                                            <td class="text-center align-middle">
                                                {!! RenderEstadoNovedad::renderNovedadIcono($fila['novedad']) !!}
                                            </td>
                                            <td>{{ $fila['patente_camion'] ?? '—' }}</td>
                                            <td>{{ $fila['fecha_hora_agendada'] ?? '—' }}</td>
                                            <td>{{ $fila['patente_rampla'] ?? '—' }}</td>
                                            <td>{{ $fila['tipo_camion'] ?? '—' }}</td>
                                            <td>{{ $fila['nombre_chofer'] ?? '—' }}</td>
                                            <td>{{ $fila['rut_chofer'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
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
