<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\OperationalParameter;
use App\Models\ProgramaDiario;
use App\Http\Controllers\ProgramaDiarioController;

class ProgramaDiarioEmitirAuto extends Command
{
    /**
     * El nombre y firma del comando de consola.
     * Ejecuta cada 5 minutos vía Scheduler.
     */
    protected $signature = 'programa-diario:emitir-auto';

    /**
     * Descripción del comando.
     */
    protected $description = 'Emite automáticamente el Programa Diario cuando el parámetro esté activo y sea la hora configurada.';

    /**
     * Lógica principal del comando.
     */
    public function handle(): int
    {
        $ahora       = Carbon::now();                          // Usa timezone de APP_TIMEZONE
        $fechaHoy    = $ahora->toDateString();                 // YYYY-MM-DD de HOY
        $fechaManana = Carbon::tomorrow()->toDateString();     // YYYY-MM-DD de mañana

        //$fechaManana = $fechaHoy;                              // Asumimos emitir el Programa Diario de HOY - SOLO PARA PRUEBAS

        // 🔎 Logger dedicado (logs/autoemit/autoemit_YYYY-MM-DD.log)
        $logDir  = storage_path('logs/autoemit');
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        $logPath = $logDir . '/autoemit_' . $fechaHoy . '.log';

        $wlog = function (string $line) use ($logPath) {
            @file_put_contents($logPath, now()->format('Y-m-d H:i:s') . ' ' . $line . PHP_EOL, FILE_APPEND);
        };

        // 👇 Log inicial de contexto
        $wlog("🔄 Inicio AutoEmit — fecha={$fechaManana}");
        Log::info('[AutoEmit] Inicio ejecución emisión', ['fecha' => $fechaManana]);

        // 1) Consultar parámetros operacionales
        $op = OperationalParameter::first();
        if (!$op) {
            $this->warn('Parámetros operacionales no encontrados. Se detiene.');
            $wlog('⚠️  OperationalParameter no encontrado. Fin.');
            Log::warning('[AutoEmit] No se encontró registro en OperationalParameter.');

            return self::SUCCESS;
        }

        $autoActivado = (bool) ($op->auto_emit_daily_program ?? false); // boolean
        if (!$autoActivado) {
            $this->info('Emisión automática desactivada. Nada que hacer.');
            $wlog('ℹ️  Emisión automática desactivada en parámetros. Fin.');
            Log::info('[AutoEmit] Emisión automática desactivada en parámetros.');

            return self::SUCCESS;
        }

        $horaConfig = $op->daily_program_execution_time ?? null; // 'HH:MM' (ej: '16:00')
        if (empty($horaConfig)) {
            $this->warn('Hora de emisión no configurada. Se detiene.');
            $wlog('⚠️  Falta daily_program_execution_time. Fin.');
            Log::warning('[AutoEmit] Falta daily_program_execution_time en OperationalParameter.');

            return self::SUCCESS;
        }

        // 2) Creamos un Carbon con la Fecha de Hoy unida a la Hora Configurada
        try {
            $horaProgramada = Carbon::parse($fechaHoy . ' ' . $horaConfig); // robusto para 'HH:MM' o 'HH:MM:SS'

        } catch (\Throwable $e) {
            $this->error('Formato invalido de daily_program_execution_time: ' . $horaConfig);
            $wlog("⛔ Hora inválida en parámetros: {$horaConfig}. Fin.");
            Log::error('[AutoEmit] Hora inválida en parámetros: ' . $horaConfig);

            return self::SUCCESS;
        }

        // 3) Verificar hora actual >= hora de emisión
        if ($ahora->lt($horaProgramada)) {
            $this->info('Aún no es hora — ahora=' . $ahora->format('H:i:s') . ', programada=' . $horaProgramada->format('H:i:s') . '. Se detiene.');
            $wlog('⏱️  Aún no es hora — ahora=' . $ahora->format('H:i:s') . ', programada=' . $horaProgramada->format('H:i:s') . '. Fin.');
            Log::info('[AutoEmit] Aún no es hora — ahora=' . $ahora->format('H:i:s') . ', programada=' . $horaProgramada->format('H:i:s') . '. Fin.');

            return self::SUCCESS;
        }

        // 4) Chequear si ya existe Programa Diario emitido para HOY (cualquier versión)
        $yaExiste = ProgramaDiario::whereDate('fecha_programa', $fechaManana)->exists();
        if ($yaExiste) {
            $this->info('Ya existe Programa Diario emitido para ' . $fechaManana . '. No se emite nuevamente.');
            $wlog("✅ Ya existe Programa Diario para {$fechaManana}. Fin.");
            Log::info('[AutoEmit] Ya existe Programa Diario para', ['fecha' => $fechaManana]);

            return self::SUCCESS;
        }

        // 4) Emitir (reutilizando tu método del controlador)
        try {
            $wlog('▶️  Emisión autorizada. Llamando a emitirProgramaDiarioAuto(' . $fechaManana . ') ...');
            Log::info('[AutoEmit] Emisión autorizada. Llamando a emitirProgramaDiarioAuto con', ['fecha' => $fechaManana]);

            // ⚠️ Pequeño ajuste recomendado: permite pasar $fechaManana a tu método (ver snippet en sección 3)
            /** @var ProgramaDiarioController $ctrl */
            $ctrl = app(ProgramaDiarioController::class);
            $response = $ctrl->emitirProgramaDiarioAuto($fechaManana); // pasamos la fecha decidida aquí

            $code    = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
            $payload = method_exists($response, 'getContent')    ? $response->getContent()    : 'OK';

            // Si por alguna razón el controlador devolviera algo que no es JSON válido
            if (null === json_decode($payload, true) && json_last_error() !== JSON_ERROR_NONE) {
                $wlog('ℹ️  Payload no-JSON recibido — HTTP ' . $code);
                Log::info('[AutoEmit] Payload no-JSON recibido — HTTP ' . $code);
            }

            // Intentar parsear JSON para extraer id/version
            $progId = $version = null;
            $data = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $progId  = $data['programa_id'] ?? null;
                $version = $data['version']     ?? null;
            
                // 👉 Nuevo: si ok=false, tratamos como "sin emisión"
                if (($data['ok'] ?? null) !== true) {
                    $reason = $data['reason'] ?? 'unknown';
                    $this->info("Sin emisión para {$fechaManana} — motivo={$reason}");
                    $this->line($payload);
                    $wlog('ℹ️  Sin emisión — motivo=' . $reason);
                    Log::info('[AutoEmit] Sin emisión', ['fecha' => $fechaManana, 'reason' => $reason, 'payload' => $data]);

                    return self::SUCCESS;
                }            
            }

            $this->info("Emitido Programa Diario {$fechaManana} — HTTP {$code}");
            $this->line($payload);

            $wlog('🏁 Resultado — HTTP ' . $code . ', programa_id=' . ($progId ?? 'null') . ', version=' . ($version ?? 'null'));
            Log::info('[AutoEmit] Emisión ejecutada', ['fecha' => $fechaManana, 'http' => $code, 'programa_id' => $progId, 'version' => $version]);

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('Error al emitir Programa Diario: ' . $e->getMessage());
            $wlog('💥 Error al emitir: ' . $e->getMessage());
            Log::error('[AutoEmit] Excepción al emitir: ' . $e->getMessage(), [
                'fecha' => $fechaManana,
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
