<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regiones';

    protected $fillable = [
        'nombre',
        'orden',
        'operativa',
    ];

    protected $casts = [
        'orden' => 'integer',
        'operativa' => 'boolean',
    ];

    /**
     * Comunas pertenecientes a la región.
     */
    public function comunas()
    {
        return $this->hasMany(Comuna::class)->orderBy('nombre');
    }
}
