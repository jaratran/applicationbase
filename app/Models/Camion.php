<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Empresa;
use App\Models\Conductor;
use App\Models\Catalogo;
use App\Models\Region;

class Camion extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'camiones';
	protected $appends = ['region_operativa_codigo']; // Esto fuerza que se incluya el accessor en JSON

    /**
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'empresa_id',
        'conductor_id',
        'tipo_camion_id',
		'region_operativa_id',   // 👈 NUEVO
        'patente',
        'patente_rampla',
        'arrendado',
        'rendimiento_optimo',
        'activo',
        'observacion_inactividad',
    ];

	/**
	 * Método getRegionOperativaCodigoAttribute (->region_operativa_codigo) en Camión para obtener el código de la región operativa fácilmente.
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
     * Relaciones Eloquent de Camion con otras tablas
     */

    // Empresa propietaria del camión
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'id');          // Un camión es propiedad de una empresa
    }

	// Región operativa del conductor
	public function regionOperativa()
	{
		return $this->belongsTo(Region::class, 'region_operativa_id', 'id');
	}

    // Conductor por defecto del camión
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'conductor_id', 'id');      // Un camión tiene un conductor por defecto
    }

    // Tipo de camión (list_parameters)
    public function tipoCamion()
    {
        return $this->belongsTo(Catalogo::class, 'tipo_camion_id', 'id');   // Un camión pertenece a un tipo de camión
    }

}
