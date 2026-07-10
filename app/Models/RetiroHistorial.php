<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetiroHistorial extends Model
{
    protected $table = 'retiros_historial';

    public $timestamps = false;

    protected $fillable = [
        'retiro_id',
        'fecha_retiro',
        'tipo_retiro_id',
        'kilogramos_estimados',
        'requiere_reposicion',
        'cantidad_bins',
        'estado_id',
        'activo',
        'usuario_id',
        'motivo_cambio',
    ];

    protected $casts = [
        'fecha_retiro' => 'datetime',
        'requiere_reposicion' => 'boolean',
        'activo' => 'boolean',
        'created_at' => 'datetime',
    ];

    // 🔁 Relaciones

    /**
     * Relación: El registro de historial de retiro pertenece a un retiro.
     */
    public function retiro(): BelongsTo
    {
        return $this->belongsTo(Retiro::class, 'retiro_id');
    }

    /**
     * Relación: El registro de historial de retiro posee un tipo de retiro.
     */
    public function tipoRetiro(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'tipo_retiro_id');
    }

    /**
     * Relación: El registro de historial de retiro posee un estado.
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'estado_id');
    }

    /**
     * Relación: El registro de historial de retiro posee un autor.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
