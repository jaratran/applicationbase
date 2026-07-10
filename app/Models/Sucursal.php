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

	/**
	 * Método getRegionOperativaCodigoAttribute (->region_operativa_codigo) en Sucursal.
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
	 * La tabla de solicitudes no tiene región propia.
	 * Pero se puede derivar de forma determinística desde:
	 *
   	 * 			Sucursal
	 * 				→ Comuna
	 * 					→ region_id
     */
	public function getRegionOperativaIdAttribute(): int
	{
		return $this->comuna?->region_id;
	}

    /**
     * Relaciones Eloquent de Sucursal con otras tablas
     */

    // Productoras de Materia Prima vinculadas (maquilas)
	public function empresasAtendidas()
	{
		return $this->belongsToMany(Empresa::class, 'maquilas', 'sucursal_id', 'empresa_id')
					->where('empresas.activo', 1)
					->withTimestamps();
	}

    // Zona (Catalogo)
    public function zona()
    {
        return $this->belongsTo(Catalogo::class, 'zona_id', 'id');             // Una sucursal pertenece a una zona
    }

    // Tipo de sucursal (Catalogo)
    public function tipoSucursal()
    {
        return $this->belongsTo(Catalogo::class, 'tipo_sucursal_id', 'id');    // Una sucursal pertenece a un tipo de sucursal
    }

    // Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);                  // Una sucursal pertenece a una comuna
    }
}
