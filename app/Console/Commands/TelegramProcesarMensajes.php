<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Illuminate\Console\Command;

use App\Models\TelegramLink;
use App\Models\Conductor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TelegramProcesarMensajes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:procesar-mensajes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs/telegram/telegram_' . now()->format('Y-m-d') . '.log');
        if (!file_exists(dirname($logPath))) {
            mkdir(dirname($logPath), 0775, true);
        }

        file_put_contents($logPath, now() . " 🔄 Procesando mensajes recibidos desde Telegram...\n", FILE_APPEND);
        
        try {
            $token = config('services.telegram.bot_token'); // Asegúrate que esto esté en config/services.php y .env
            
            file_put_contents($logPath, now() . " ℹ️ Token usado: {$token}\n", FILE_APPEND);

            $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates", [
                                                                                        'timeout' => 10,            // el offset se aplicará después de procesar los updates
                                                                                    ]);

            if ($response->failed()) {
                file_put_contents($logPath, now() . "❌ Error al obtener mensajes desde Telegram\n", FILE_APPEND);

                Log::error('[Telegram] Falló el llamado a getUpdates: ' . $response->body());
                return;
            }

            $data = $response->json();

            if (!isset($data['result'])) {
                file_put_contents($logPath, now() . "⚠️ No se encontraron mensajes recientes.\n", FILE_APPEND);

                return;
            }

            file_put_contents($logPath, now() . '✅ Mensajes obtenidos: ' . count($data['result']) . "\n", FILE_APPEND);

            // Iterar y detectar /vincular {PIN}
            $lastUpdateId = null;
            foreach ($data['result'] as $update) {
                // 👇 Acumular el update_id máximo en cada iteración
                $lastUpdateId = max($lastUpdateId, $update['update_id']);

                // Verificamos si hay mensaje con texto
                if (!isset($update['message']['text']) || !isset($update['message']['chat']['id'])) {
                    continue;
                }

                $texto   = trim($update['message']['text']);
                $chat_id = $update['message']['chat']['id'];

                // Detectar si el mensaje es del tipo "/vincular 123456"
                if (preg_match('/^\/vincular\s+(\d{6})$/i', $texto, $matches)) {
                    $pin = $matches[1];

                    file_put_contents($logPath, now() . "📩 Comando recibido: /vincular {$pin} desde chat_id: {$chat_id}\n", FILE_APPEND);

                    // 👉 Aquí procesamos la validación del PIN y la vinculación
                    $link = TelegramLink::where('pin', $pin)
                                            ->where('estado', 'pendiente')
                                            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                                            ->first();

                    if (!$link) {
                        file_put_contents($logPath, now() . "❌ PIN inválido o expirado: {$pin}\n", FILE_APPEND);

                        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                            'chat_id' => $chat_id,
                            'text'    => __('responses.telegram.pin_invalido'),
                        ]);

                        continue;
                    }

                    // Guardar el chat_id en el conductor, actualizar el estado del link, y enviar confirmación al usuario 🛠️📨

                    // 1. Buscar el conductor
                    $conductor = Conductor::find($link->conductor_id);

                    if (!$conductor) {
                        file_put_contents($logPath, now() . "❌ Conductor no encontrado para el link con PIN {$pin}\n", FILE_APPEND);

                        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                            'chat_id' => $chat_id,
                            'text'    => __('responses.telegram.error_vinculacion'),
                        ]);

                        continue;
                    }

                    // 2. Guardar el chat_id en el conductor
                    $conductor->telegram_chat_id = $chat_id;
                    $conductor->save();

                    // 3. Marcar el link como vinculado y se rellenan los demás campos de auditoria
                    $link->update([
                        'estado'             => 'vinculado',
                        'chat_id'            => $chat_id,
                        'fecha_vinculacion'  => now(),
                        'intentos'           => DB::raw('intentos + 1'),
                    ]);

                    file_put_contents($logPath, now() . "📝 Link actualizado: estado=vinculado, chat_id={$chat_id}, fecha_vinculacion=now(), intentos++\n", FILE_APPEND);

                    // 4. Responder mensaje al usuario en Telegram
                    Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chat_id,
                        'text'    => __('responses.telegram.vinculacion_exitosa', [
                            'nombre' => $conductor->nombre . ' ' . $conductor->apellido,
                        ]),
                    ]);

                    file_put_contents($logPath, now() . "✅ Conductor {$conductor->nombre} {$conductor->apellido} (ID {$conductor->id}) vinculado correctamente con chat_id {$chat_id}\n", FILE_APPEND);
                }
            }

            // 👇 Al finalizar el foreach, limpiar la cola de Telegram mediante la confirmación a Telegram que ya procesamos todo hasta ese ID
            if ($lastUpdateId) {
                Http::get("https://api.telegram.org/bot{$token}/getUpdates", [
                    'offset' => $lastUpdateId + 1,
                ]);

                file_put_contents($logPath, now() . "🧹 Cola de mensajes limpiada hasta update_id: {$lastUpdateId}\n", FILE_APPEND);
            }            

        } catch (\Throwable $e) {
            file_put_contents($logPath, now() . "❌ Excepción procesando mensajes Telegram.\n", FILE_APPEND);

            Log::error('[Telegram] Excepción: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
