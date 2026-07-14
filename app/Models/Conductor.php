<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\Empresa;
use App\Models\Region;

class Conductor extends Model
{
	/**
	 * Tabla asociada al modelo.
	 *
	 * @var string
	 */
	protected $table = 'conductores';
	protected $appends = ['nombre_completo', 'region_operativa_codigo']; // Esto fuerza que se incluya el accessor en JSON

	/**
	 * Atributos que pueden ser asignados masivamente.
	 *
	 * @var array
	 */
	protected $fillable = [
		'empresa_id',
		'region_operativa_id',   // 👈 NUEVO
		'rut',
		'nombre',
		'apellido',
		'telefono',
		'activo',
		'observacion_inactividad',
	];

	/**
	 * Método getNombreCompletoAttribute (->nombre_completo) en Conductor para combinar nombre y apellido fácilmente.
	 *
	 */
	public function getNombreCompletoAttribute()
	{
		return "{$this->nombre} {$this->apellido}";
	}

	/**
	 * Método getRegionOperativaCodigoAttribute (->region_operativa_codigo) en Conductor para obtener el código de la región operativa fácilmente.
	 *
	 */
	public function getRegionOperativaCodigoAttribute()
	{
		$map = [
			config('constantes.REGION_NI')   => 'NI',
			config('constantes.REGION_I')    => 'I',
			config('constantes.REGION_II')   => 'II',
			config('constantes.REGION_III')  => 'III',
			config('constantes.REGION_IV')   => 'IV',
			config('constantes.REGION_V')    => 'V',
			config('constantes.REGION_VI')   => 'VI',
			config('constantes.REGION_VII')  => 'VII',
			config('constantes.REGION_VIII') => 'VIII',
			config('constantes.REGION_IX')   => 'IX',
			config('constantes.REGION_X')    => 'X',
			config('constantes.REGION_XI')   => 'XI',
			config('constantes.REGION_XII')  => 'XII',
			config('constantes.REGION_RM')   => 'RM',
			config('constantes.REGION_XIV')  => 'XIV',
			config('constantes.REGION_XV')   => 'XV',
		];

		return $map[$this->region_operativa_id] ?? 'NA';
	}

	/**
	 * Envío de mensaje por Telegram al Conductor
	 *
	 */
	public function notificarPorTelegram(string $mensaje, ?int $chatId = null): bool
	{
		$chatId = $chatId ?? $this->telegram_chat_id;

		if (empty($chatId)) {
			return false;
		}

		try {
			$token = config('services.telegram.bot_token');

			Log::info('[Telegram] Preparando envío de notificación a conductor', [
				'token'   => $token,
				'chat_id' => $chatId
			]);

			$response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
				'chat_id' => $chatId,
				'text'    => $mensaje,
			]);

			return $response->successful(); // ✅ true si HTTP status 2xx

		} catch (\Throwable $e) {
			Log::warning('[Telegram] No se pudo notificar al conductor vía Telegram', [
				'conductor_id' => $this->id,
				'chat_id'      => $chatId,
				'exception'    => $e->getMessage(),
			]);

			return false;
		}
	}

	/**
	 * Relaciones Eloquent de Conductor con otras tablas
	 *
	 */
	// Empresa que emplea al conductor
	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'empresa_id', 'id');          // Un conductor es empleado por una empresa
	}

	// Región operativa del conductor
	public function regionOperativa()
	{
		return $this->belongsTo(Region::class, 'region_operativa_id', 'id');
	}


	// Planificaciones asignadas a este conductor
	public function planificaciones()
	{
		return $this->hasMany(Planificacion::class, 'conductor_id');
	}

	/**
	 * Todos los vínculos Telegram históricos
	 */
	public function telegramLinks()
	{
		return $this->hasMany(TelegramLink::class, 'conductor_id');
	}

	/**
	 * Último vínculo activo (estado vinculado)
	 */
	public function telegramLinkActivo()
	{
		return $this->hasOne(TelegramLink::class, 'conductor_id')->where('estado', 'vinculado');
	}
}
