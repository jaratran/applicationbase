<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramLink extends Model
{
    protected $table = 'telegram_links';

    protected $fillable = [
        'conductor_id',
        'pin',
        'chat_id',
        'estado',
        'fecha_generacion',
        'fecha_vinculacion',
        'intentos',
    ];

    protected $casts = [
        'fecha_generacion'   => 'datetime',
        'fecha_vinculacion'  => 'datetime',
    ];

    /**
     * Relación con el conductor
     */
    public function conductor(): BelongsTo
    {
        return $this->belongsTo(Conductor::class, 'conductor_id');
    }
}
