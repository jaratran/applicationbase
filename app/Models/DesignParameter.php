<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignParameter extends Model
{
    private const DEFAULT_IMAGES = [
        'logo_design' => 'default_logo.png',
        'emblema_design' => 'default_emblema.png',
        'favicon_design' => 'default_favicon.ico',
        'fondo_pantalla_design' => 'default_fondo.png',
    ];

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'design_parameters';

    /**
     * Atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'titulo_design',
        'logo_design',
        'emblema_design',
        'favicon_design',
        'fondo_pantalla_design',
        'custom_primary',
        'custom_secondary',
        'custom_success',
        'custom_warning',
        'custom_danger',
        'custom_info'
    ];

    public function getLogoDesignAttribute(?string $value): string
    {
        return $this->availableImage($value, self::DEFAULT_IMAGES['logo_design']);
    }

    public function getEmblemaDesignAttribute(?string $value): string
    {
        return $this->availableImage($value, self::DEFAULT_IMAGES['emblema_design']);
    }

    public function getFaviconDesignAttribute(?string $value): string
    {
        return $this->availableImage($value, self::DEFAULT_IMAGES['favicon_design']);
    }

    public function getFondoPantallaDesignAttribute(?string $value): string
    {
        return $this->availableImage($value, self::DEFAULT_IMAGES['fondo_pantalla_design']);
    }

    private function availableImage(?string $value, string $default): string
    {
        if (!$value || basename($value) !== $value || !is_file(public_path('config/' . $value))) {
            return $default;
        }

        return $value;
    }
}
