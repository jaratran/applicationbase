<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Region;
use App\Models\Catalogo;

class Rampla extends Model
{
	/**
	 * Tabla asociada al modelo.
	 *
	 * @var string
	 */
	protected $table = 'ramplas';
	protected $appends = ['region_operativa_codigo']; // Esto fuerza que se incluya el accessor en JSON

	/**
	 * Atributos asignables masivamente.
	 *
	 * @var array
	 */
	protected $fillable = [
		'patente',
		'region_operativa_id',
		'tipo_rampla_id',
		'capacidad_rampla_id',
		'estado_rampla_id',
		'activo',
		'observacion_inactividad',
	];

	/**
	 * Método getRegionOperativaCodigoAttribute (->region_operativa_codigo) en Rampla para obtener el código de la región operativa fácilmente.
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
	 * Relaciones Eloquent
	 */

	// Región operativa
	public function regionOperativa()
	{
		return $this->belongsTo(Region::class, 'region_operativa_id', 'id');
	}

	// Tipo de rampla (catálogo)
	public function tipoRampla()
	{
		return $this->belongsTo(Catalogo::class, 'tipo_rampla_id', 'id');
	}

	// Capacidad de rampla (catálogo)
	public function capacidadRampla()
	{
		return $this->belongsTo(Catalogo::class, 'capacidad_rampla_id', 'id');
	}

	// Estado de la rampla (catálogo)
	public function estadoRampla()
	{
		return $this->belongsTo(Catalogo::class, 'estado_rampla_id', 'id');
	}
}
