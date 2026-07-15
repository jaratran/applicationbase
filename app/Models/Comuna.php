<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Region;

class Comuna extends Model
{
    protected $table = 'comunas';

    protected $fillable = [
        'nombre',
        'region_id',
    ];

    protected $casts = [
        'region_id' => 'integer',
    ];

    /**
     * Región a la que pertenece la comuna.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
