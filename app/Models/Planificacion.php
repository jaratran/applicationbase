<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\ProgramaDiarioDetalle;

class Planificacion extends Model
{
    protected $table = 'planificaciones';

    protected $fillable = [
		'retiro_id',									// ← ID recién creado
		'region_operativa_id',

		'fecha_hora_planificada',

		'tipo_operacion',

		'duracion_viaje',
		'hora_llegada_estimada',
		'duracion_estimada_dias',
		'eta_calculada',

		'tipo_materia_prima_id',
		'especie_id',
		'tiene_restriccion',

		'tipo_transporte_id',
		'fecha_embarque',
		'fecha_arribo_puerto',

		'rampla_id',
		'estado_rampla_id',
		'camion_id',
		'patente_rampla',
		'conductor_id',

		'fecha_rescate_puerto',
		'camion_rescate_id',
		'conductor_rescate_id',

		'motivo_modificacion_id',						// Se está creando así que aún no hay motivo de modificación
		'estado_id',									// Se salta los estados Esperando, Comentado, Aceptado
		'ticket_cierre',
		'activo'
	];

	protected $casts = [
		'fecha_hora_planificada'   => 'datetime',

		'tipo_operacion'           => 'integer',

		'fecha_embarque'           => 'datetime',
		'fecha_arribo_puerto'      => 'datetime',
		'fecha_rescate_puerto'     => 'datetime',
		'hora_llegada_estimada'    => 'datetime',
		'eta_calculada'            => 'datetime',
		'fecha_liberacion_recursos'=> 'datetime',

		'tiene_restriccion'        => 'boolean',
		'activo'                   => 'boolean',
	];

	protected $appends = [
		'camion_rescate_patente',
		'conductor_rescate_nombre',
	];

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

	// Accessor: patente de rampla vía rampla_id
	public function getPatenteRamplaAttribute(): ?string
	{
		switch ($this->region_operativa_id) {

			case config('constantes.REGION_X'):
				return $this->attributes['patente_rampla'] ?? null;

			case config('constantes.REGION_XII'):
				return $this->rampla?->patente;

			default:
				return null;

		}
	}

	// Accessor: duración de viaje desde los respectivos campos según la Región Operativa
	public function getDuracionAttribute(): int|string|null
	{
		switch ($this->region_operativa_id) {

			case config('constantes.REGION_X'):
				return $this->duracion_viaje; // HH:MM (string)

			case config('constantes.REGION_XII'):
				return $this->duracion_estimada_dias; // int (días)

			default:
				return null;

		}
	}

	// Accessor: fecha y hora de llegada a planta La Portada desde los respectivos campos según la Región Operativa
	public function getFechaHoraLlegadaAttribute(): ?\Carbon\Carbon
	{
		switch ($this->region_operativa_id) {

			case config('constantes.REGION_X'):
				return $this->hora_llegada_estimada;

			case config('constantes.REGION_XII'):
				return $this->eta_calculada;

			default:
				return null;

		}
	}

	// Accessor: Patente camión de rescate (plano)
	public function getCamionRescatePatenteAttribute(): ?string
	{
		return $this->camionRescate?->patente;
	}

	//  Accessor: Nombre fconductor de rescate (plano)
	public function getConductorRescateNombreAttribute(): ?string
	{
		return $this->conductorRescate?->nombre_completo;
	}


    // Modelo Planificacion sincroniza el estado del retiro con el estado actual de la planificación.
    public function sincronizarEstadoRetiro(string $comentario_anulacion = ''): void
    {
        // $this->loadMissing('retiro'); // ✅ Asegura que la relación esté disponible

        if (isset($this->retiro) && isset($this->estado_id)) {

            if ( in_array( $this->estado_id, [  config('constantes.CATALOGO_NO_ESPECIFICADO'),              // Si el estado de la planificacón es CERO (CRUDA, VACIA) la ABORTARON
                                                config('constantes.ESTADO_RETIRO_CANCELADO')    ]) ) {      // Si es CANCELADO la CANCELARON
                $datos = [
                    'estado_id'             => config('constantes.ESTADO_RETIRO_CANCELADO'),                    // Marcamos el estado del retiro como CANCELADO
                    'activo'                => false,                                                           // Marcamos el estado del registro del retiro como inactivo
                    'comentario_anulacion'  => $comentario_anulacion,                                           // Y le ponemos el comentario ingresado por el usuario
                ];
            }
            else{                                                                                           // El estado de la PLANIFICACION es significativo
                $datos = [                                                                                      // (PLANIFICADA, PROGRAMADA, TERMINADA)
                    'estado_id'             => $this->estado_id,                                                // Se lo copiamos al RETIRO
                ];
            }

            $this->retiro->update($datos);
        }
    }

    /**
     * Método: Propaga el estado actual de la planificación a todos los registros
     * de programa diario detalle que estén asociados al retiro correspondiente.
     */
    public function sincronizarEstadosDetallesProgramasDiarios(): void
    {
        if ($this->retiro && $this->estado_id) {
            ProgramaDiarioDetalle::where('retiro_id', $this->retiro_id)
                ->update(['estado' => $this->estado_id]);
        }
    }

    // RELACIONES

    // Relación con el retiro al cual corresponde esta planificación
    public function retiro() {
        return $this->belongsTo(Retiro::class, 'retiro_id');
    }

    // Relación con el catálogo de especies (ej. Manzana, Pera, etc.)
    public function especie()
    {
        return $this->belongsTo(Catalogo::class, 'especie_id');
    }

    // Relación con el catálogo de tipos de materia prima (ej. Fruta fresca, Bins sueltos, etc.)
    public function tipoMateriaPrima()
    {
        return $this->belongsTo(Catalogo::class, 'tipo_materia_prima_id');
    }

    // Relación con el catálogo de estados de planificación (ej. Pendiente, Confirmada, Cancelada, etc.)
    public function estado()
    {
        return $this->belongsTo(Catalogo::class, 'estado_id');
    }

    // Relación con el camión asignado a la planificación
    public function camion()
    {
        return $this->belongsTo(Camion::class, 'camion_id');
    }

    // Relación con el conductor asignado al retiro
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'conductor_id');
    }

    // Relación con el catálogo de motivos de modificación del registro
    public function motivoModificacion()
    {
        return $this->belongsTo(Catalogo::class, 'motivo_modificacion_id');
    }

	// Usuarios
	public function planificadoPor()
	{
		return $this->belongsTo(User::class, 'planificado_por_user_id');
	}

	// Transporte / logística Región XII
	public function tipoTransporte()
	{
		return $this->belongsTo(Catalogo::class, 'tipo_transporte_id');
	}

	public function rampla()
	{
		return $this->belongsTo(Rampla::class, 'rampla_id');
	}

	public function estadoRampla()
	{
		return $this->belongsTo(Catalogo::class, 'estado_rampla_id');
	}

	// Rescate (camión y conductor)
	public function camionRescate()
	{
		return $this->belongsTo(Camion::class, 'camion_rescate_id');
	}

	public function conductorRescate()
	{
		return $this->belongsTo(Conductor::class, 'conductor_rescate_id');
	}

	// ETA / auditoría
	public function etaMotivoModificacion()
	{
		return $this->belongsTo(Catalogo::class, 'eta_motivo_modificacion_id');
	}

}
