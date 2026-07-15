<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regiones';

    protected $fillable = [
        'id',
        'nombre',
        'orden'
    ];

	// Se refiere a las regiones operativas para cursar retiros.
    protected $casts = [
        'operativa' => 'boolean',
    ];

    /**
     * Relaciones Eloquent de Regiones con otras tablas
     *
     * @var array
     */
    public function comunas()
    {
        return $this->hasMany(Comuna::class)->orderBy('nombre');
    }
}
