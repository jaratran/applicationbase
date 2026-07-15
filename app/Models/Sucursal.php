<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Empresa;
use App\Models\Comuna;
use App\Models\Catalogo;

class Sucursal extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'sucursales';

    /**
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'zona_id',
        'nombre_sucursal',
        'codigo_siep',
        'tipo_sucursal_id',
        'comuna_id',
        'telefono',
        'email',
        'km',
        'tiempo_estimado_viaje',
        'activo',
        'observacion_inactividad',
    ];

    protected $casts = [
        'zona_id' => 'integer',
        'codigo_siep' => 'integer',
        'tipo_sucursal_id' => 'integer',
        'comuna_id' => 'integer',
        'km' => 'integer',
        'tiempo_estimado_viaje' => 'decimal:2',
        'activo' => 'boolean',
    ];

	/**
	 * Código territorial derivado de la región de la sucursal.
	 */
	public function getRegionOperativaCodigoAttribute(): string
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
     * Accesor “región operativa” de una Sucursal.
	 *
	 * Se deriva de forma determinística desde:
	 *
   	 * 			Sucursal
	 * 				→ Comuna
	 * 					→ region_id
     */
	public function getRegionOperativaIdAttribute(): ?int
	{
		return $this->comuna?->region_id;
	}

	/**
	 * Empresas productoras vinculadas mediante maquilas.
	 */
	public function empresasAtendidas()
	{
		return $this->empresasVinculadas()
					->where('empresas.activo', true)
					->wherePivot('activo', true);
	}

	/**
	 * Todas las empresas vinculadas, incluso las inactivas.
	 */
	public function empresasVinculadas()
	{
		return $this->belongsToMany(Empresa::class, 'maquilas', 'sucursal_id', 'empresa_id')
					->withTimestamps();
	}

    /**
     * Clasificación territorial de la sucursal.
     */
    public function zona()
    {
        return $this->belongsTo(Catalogo::class, 'zona_id', 'id');
    }

    /**
     * Clasificación funcional de la sucursal.
     */
    public function tipoSucursal()
    {
        return $this->belongsTo(Catalogo::class, 'tipo_sucursal_id', 'id');
    }

    /**
     * Comuna donde se ubica la sucursal.
     */
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }
}
