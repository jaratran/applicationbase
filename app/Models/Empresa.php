<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Sucursal;
use App\Models\Catalogo;
use App\Models\Comuna;

class Empresa extends Model
{
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'empresas';

    /**
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'tipo_empresa_id',
        'rut_empresa',
        'razon_social',
        'direccion',
        'comuna_id',
        'telefono',
        'email_contacto',
        'telefono_contacto',
        'activo',
        'observacion_inactividad',
    ];

    protected $casts = [
        'tipo_empresa_id' => 'integer',
        'comuna_id' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Plantas de proceso vinculadas mediante maquilas.
     */
    public function plantasProcesadoras()
    {
        return $this->sucursalesVinculadas()
					->where('sucursales.activo', true)
					->wherePivot('activo', true);
    }

    /**
     * Todas las sucursales vinculadas, incluso las inactivas.
     */
    public function sucursalesVinculadas()
    {
        return $this->belongsToMany(Sucursal::class, 'maquilas', 'empresa_id', 'sucursal_id')
                    ->withTimestamps();
    }

    /**
     * Clasificación organizacional de la empresa.
     */
    public function tipoEmpresa()
    {
        return $this->belongsTo(Catalogo::class, 'tipo_empresa_id', 'id');
    }

    /**
     * Comuna de la dirección principal.
     */
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }

}
