<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\RetiroHistorial;
use App\Models\RetiroComentario;

class Retiro extends Model
{
    use HasFactory;

    protected $table = 'retiros';

    protected $casts = [
        'fecha_retiro' => 'datetime',       // Para usar format sin tener que invocar a Carbon explicitamente
		'tipo_operacion' => 'integer',
    ];

    protected $fillable = [
        'solicitud_id',
        'fecha_retiro',
        'tipo_retiro_id',
        'kilogramos_estimados',
        'requiere_reposicion',
        'cantidad_bins',

		'tipo_operacion',

		'estado_id',
        'activo',
        'comentario_anulacion',
    ];

    /**
     * Accesor: el sistema conceptualmente considera que el retiro pertenece a una región.
     */
	public function getRegionOperativaIdAttribute()
	{
		return $this->solicitud->region_operativa_id ?? null;
	}

    /**
     * Método de dominio: el retiro considera el ir a buscar kilogramos de de materia prima.
	 *                    Evitamos usar números directamente en la lógica.
     */
	public function esRetiro(): bool
	{
		return $this->tipo_operacion === config('constantes.TIPO_OPERACION_RETIRO');
	}

    /**
     * Método de dominio: el retiro NO considera el ir a buscar kilogramos de de materia prima.
     *                    Sólo se considera la reposición de BINes (Región XII).
	 *                    Evitamos usar números directamente en la lógica.
     */
	public function esReposicion(): bool
	{
		return $this->tipo_operacion === config('constantes.TIPO_OPERACION_REPOSICION');
	}

    /**
     * Accesor: Para blades y dataTables.
     */
	public function getTipoOperacionLabelAttribute(): string
	{
		return match ($this->tipo_operacion) {
			config('constantes.TIPO_OPERACION_RETIRO') => 'Retiro',
			config('constantes.TIPO_OPERACION_REPOSICION') => 'Reposición',
			default => 'Desconocido',
		};
	}

    /**
     * Método: Para guardar en el historial una copia del actual retiro.
     */
    public function guardarHistorial(string $motivo = null, int $usuarioId = null): void
    {
        RetiroHistorial::create([
            'retiro_id'           => $this->id,
            'fecha_retiro'        => $this->fecha_retiro,
            'tipo_retiro_id'      => $this->tipo_retiro_id,
            'kilogramos_estimados'=> $this->kilogramos_estimados,
            'requiere_reposicion' => $this->requiere_reposicion,
            'cantidad_bins'       => $this->cantidad_bins,
            'estado_id'           => $this->estado_id,
            'activo'              => $this->activo,
            'usuario_id'          => $usuarioId ?? Auth::id(),
            'motivo_cambio'       => $motivo,
        ]);
    }

    /**
     * Método: Para guardar en los comentarios el actual comentario.
     */
    public function guardarComentario(string $comentario, int $usuarioId = null): void
    {
        RetiroComentario::create([
            'retiro_id'   => $this->id,
            'usuario_id'  => $usuarioId ?? Auth::id(),
            'comentario'  => $comentario,
            'created_at'  => now(),
        ]);
    }

    /**
     * Método: Para crear la planificación inicial asociada al retiro.
     */
	public function crearPlanificacionInicial(): void
	{
		if ($this->planificacion) {
			return; // Ya existe, no se debe duplicar
		}

		$regionOperativa = $this->solicitud->region_operativa_id;

		// El registro de planificación se debe crear vacío, limpio, en blanco, etc.
		switch ($regionOperativa) {
			case config('constantes.REGION_X'):
				Planificacion::create([
					'retiro_id'               => $this->id,                                     // ✔️ FK a retiros.id, protegido con ON DELETE CASCADE
					'region_operativa_id'     => $regionOperativa,                              // Regió Operativa, campo que además de Solicitud también lo posee Planificación

					'fecha_hora_planificada'  => Carbon::create(1970, 1, 1, 0, 0, 0),           // ✔️ Obligatorio, valor válido

					'tipo_operacion'          => $this->tipo_operacion,

					'duracion_viaje'          => '00:00',                                       // ✔️ Obligatorio, valor válido
					'hora_llegada_estimada'   => Carbon::create(1970, 1, 1, 0, 0, 0),           // ✔️ Obligatorio, valor válido

					'especie_id'              => config('constantes.CATALOGO_NO_ESPECIFICADO'), // ⚠️ FK -> catalogos.id
					'tipo_materia_prima_id'   => config('constantes.CATALOGO_NO_ESPECIFICADO'), // ⚠️ FK -> catalogos.id
					'tiene_restriccion'       => false,

					'camion_id'               => 0,                                             // ⚠️ FK -> camiones.id
					'patente_rampla'          => null,
					'conductor_id'            => 0,                                             // ⚠️ FK -> conductores.id

					'motivo_modificacion_id'  => config('constantes.CATALOGO_NO_ESPECIFICADO'), // ⚠️ FK -> catalogos.id
					'estado_id'               => config('constantes.CATALOGO_NO_ESPECIFICADO'), // ⚠️ FK -> catalogos.id
					'activo'                  => true,
				]);
				break;

			case config('constantes.REGION_XII'):
				Planificacion::create([
					'retiro_id'               => $this->id,                                     // ✔️ FK a retiros.id, protegido con ON DELETE CASCADE
					'region_operativa_id'     => $regionOperativa,                              // Regió Operativa, campo que además de Solicitud también lo posee Planificación

					'fecha_hora_planificada'  => Carbon::create(1970, 1, 1, 0, 0, 0),

					'tipo_operacion'          => $this->tipo_operacion,

					'duracion_estimada_dias'  => 0,
					'eta_calculada'           => Carbon::create(1970, 1, 1, 0, 0, 0),

					'tipo_materia_prima_id'   => config('constantes.CATALOGO_NO_ESPECIFICADO'),
					'especie_id'              => config('constantes.CATALOGO_NO_ESPECIFICADO'),
					'tiene_restriccion'       => false,

					'tipo_transporte_id'      => config('constantes.CATALOGO_NO_ESPECIFICADO'),
					'fecha_embarque'          => null,
					'fecha_arribo_puerto'     => null,

					'rampla_id'               => 0,
					'estado_rampla_id'        => config('constantes.CATALOGO_NO_ESPECIFICADO'),
					'camion_id'               => 0,
					'conductor_id'            => 0,

					'fecha_rescate_puerto'    => null,
					'camion_rescate_id'       => 0,
					'conductor_rescate_id'    => 0,

					'motivo_modificacion_id'  => config('constantes.CATALOGO_NO_ESPECIFICADO'),
					'estado_id'               => config('constantes.CATALOGO_NO_ESPECIFICADO'),
					'activo'                  => true,
				]);
				break;

			default:
				throw new \LogicException(
					"Región operativa no soportada al crear planificación inicial (Retiro ID {$this->id})"
				);
		}
	}

	/**
	 * Scope: Limita los retiros a las regiones operativas del usuario en sesión.
	 */
	public function scopePorRegionesOperativas($query, array $regionesOperativasIds)
	{
		// 🧠 Fail-safe: si no hay regiones, no se devuelve nada
		if (empty($regionesOperativasIds)) {
			return $query->whereRaw('1 = 0');
		}

		return $query->whereHas('solicitud.maquila.sucursal.comuna', function ($q) use ($regionesOperativasIds) {
			$q->whereIn('region_id', $regionesOperativasIds);
		});
	}

    /**
     * Relación: Un retiro pertenece a una solicitud.
     */
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    /**
     * Relación: El tipo de retiro proviene del catálogo.
     */
    public function tipoRetiro()
    {
        return $this->belongsTo(Catalogo::class, 'tipo_retiro_id');
    }

    /**
     * Relación: El estado proviene del catálogo.
     */
    public function estado()
    {
        return $this->belongsTo(Catalogo::class, 'estado_id');
    }

    /**
     * Relación: El retiro posee registros en el historial de retiros.
     */
    public function historial()
    {
        return $this->hasMany(RetiroHistorial::class, 'retiro_id');
    }

    /**
     * Comentarios asociados a este retiro.
     */
    public function comentarios()
    {
        return $this->hasMany(RetiroComentario::class)->orderByDesc('id');
    }

    /**
     * Relación uno a uno: cada retiro tiene UNA planificación
     */
    public function planificacion()
    {
        return $this->hasOne(Planificacion::class);
    }

}
