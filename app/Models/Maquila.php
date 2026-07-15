<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Empresa;
use App\Models\Sucursal;

class Maquila extends Model
{
    protected $table = 'maquilas';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'fecha_inicio',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'empresa_id' => 'integer',
        'sucursal_id' => 'integer',
        'fecha_inicio' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * Empresa participante de la asociación.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Sucursal participante de la asociación.
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
