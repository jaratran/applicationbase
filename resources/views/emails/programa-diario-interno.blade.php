<!DOCTYPE html>
@php
    use App\Helpers\RenderEstadoNovedad;
@endphp

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Programa Diario Emitido – {{ \Carbon\Carbon::parse($programaDiario->fecha_programa)->format('d-m-Y') }} (Versión {{ $programaDiario->version }})</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="700" style="background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <p>Estimados/as,</p>

                            <p>
                                Se ha emitido el Programa Diario de Retiros de Materia Prima para la fecha {{ \Carbon\Carbon::parse($programaDiario->fecha_programa)->format('d-m-Y') }}. A continuación, se detallan los kilos a recepcionar. 
                            </p>

                            <h4 style="margin-bottom: 10px;">📋 Detalle de Retiros:</h4>

                            @php
                                // Día base del programa
                                $base     = \Carbon\Carbon::parse($programaDiario->fecha_programa)->startOfDay();
                                $lim16    = $base->copy()->setTime(16, 0, 0);     // 16:00 del día base
                                $finDia   = $base->copy()->setTime(23, 59, 59);   // 23:59:59 del día base

                                // Acumuladores por tramo (pivot = ETA / Hora de Llegada Estimada)
                                $acum_00_16  = 0; // [00:00, 15:59]
                                $acum_16_24  = 0; // [16:00, 23:59]
                                $acum_24p    = 0; // > 24:00 (ETA día siguiente)

                                foreach ($detalles as $fila) {
                                    $etaStr = $fila['eta'] ?? null;
                                    if (!$etaStr) { continue; }

                                    try { $eta = \Carbon\Carbon::parse($etaStr); } catch (\Exception $e) { continue; }

                                    $kg = (float)($fila['kg_estimados'] ?? 0);

                                    if ($eta->isSameDay($base) && $eta->lt($lim16)) {
                                        // 00:00 - 15:59 del día base
                                        $acum_00_16 += $kg;
                                    } elseif ($eta->isSameDay($base) && $eta->between($lim16, $finDia)) {
                                        // 16:00 - 23:59 del día base
                                        $acum_16_24 += $kg;
                                    } elseif ($eta->gt($finDia)) {
                                        // Pasado 24:00 del día base (cualquier hora del día siguiente o posteriores)
                                        $acum_24p += $kg;
                                    }
                                }
                            @endphp

                            <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                                <thead style="background-color: #f0f0f0;">
                                    <tr>
                                        <th style="border: 1px solid #ccc;">Desde</th>
                                        <th style="border: 1px solid #ccc;">Hasta</th>
                                        <th style="border: 1px solid #ccc;">Cantidad (Kilos)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid #ccc;">00:00</td>
                                        <td style="border: 1px solid #ccc;">15:59</td>
                                        <td style="border: 1px solid #ccc;"><strong>{{ number_format($acum_00_16, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ccc;">16:00</td>
                                        <td style="border: 1px solid #ccc;">23:59</td>
                                        <td style="border: 1px solid #ccc;"><strong>{{ number_format($acum_16_24, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ccc;" colspan="2">Después de las 24:00</td>
                                        <td style="border: 1px solid #ccc;"><strong>{{ number_format($acum_24p, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>

                            <p style="margin-top: 30px;">Para más detalles, puede acceder directamente al programa haciendo clic en el siguiente botón:</p>
                            <p>
                                <a href="{{ $urlDestino }}" style="display: inline-block; padding: 10px 20px; background-color: {{ $designParameter->custom_primary }}; color: white; text-decoration: none; border-radius: 5px;">
                                    Ver Programa Diario
                                </a>
                            </p>

                            <p style="margin-top: 30px;">
                                Ante cualquier duda, favor contactar al Coordinador de Retiros de Materia Prima de La Portada.
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
