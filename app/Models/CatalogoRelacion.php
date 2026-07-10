<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Catalogo;

class CatalogoRelacion extends Model
{
    use SoftDeletes;

    protected $table = 'catalogo_relaciones';

    protected $fillable = [
        'valor_origen_id',
        'valor_destino_id',
        'tipo_relacion_id',
        'orden',
        'activo',
    ];

    /**
     * Valor origen de la relación (agrupador).
     */
    public function origen()
    {
        return $this->belongsTo(Catalogo::class, 'valor_origen_id');
    }

    /**
     * Valor destino de la relación (subordinado).
     */
    public function destino()
    {
        return $this->belongsTo(Catalogo::class, 'valor_destino_id');
    }

    /**
     * Tipo de la relación (ej: Agrupamiento, Complemento).
     */
    public function tipo()
    {
        return $this->belongsTo(Catalogo::class, 'tipo_relacion_id');
    }

    /**
     * Scope para solo relaciones activas.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
