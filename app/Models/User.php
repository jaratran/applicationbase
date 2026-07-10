<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Catalogo;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Comuna;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rut_usuario',
        'nombre_usuario',
        'apellidos_usuario',
        'rol_id',
        'empresa_id',
        'sucursal_id',
        'email',
        'telefono',
        'comuna_id',
        'direccion',
        'avatar',
        'es_admin',
        'activated',
        'fecha_login',
        'remember_token',
        'password',
        'activo',
        'observacion_inactividad',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'rol_id' => 'integer', // Esto se agrega por comparaciones de rol en el envío de correo de bienvenida (en la asignación de texto-email se usa 'match' para subordinarla al rol).
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Agregamos esto para que aparezca en el handlebar.
     *
     * @var array
     */
	protected $appends = [
		'region_operativa_codigo',
	];


    /**
     * Método getNombreCompletoAttribute (->nombre_completo) en Conductor para combinar nombre y apellido fácilmente.
     *
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre_usuario} {$this->apellidos_usuario}";
    }

    /**
     * Obtener el email que se usará para la verificación.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

	/**
	 * Código(s) de región operativa en formato compacto (X, XII, X/XII, NI).
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

		$regiones = $this->regiones_operativas_ids ?? [];

		if (empty($regiones)) {
			return 'NI';
		}

		$codigos = collect($regiones)
			->map(fn ($id) => $map[$id] ?? null)
			->filter()
			->values();

		return $codigos->isEmpty()
			? 'NI'
			: $codigos->implode('/');
	}

	/**
	 * Regiones operativas que el usuario puede gestionar
	 *
	 * IMPORTANTE:
	 * 		Cuidar que esté sincronizado con 'const REGION_OPERATIVA_POR_ROL'
	 *		definida en [resources\views\includes\constantes-js-catalogo.blade.php]
	 *
	 */
	public function getRegionesOperativasIdsAttribute(): array
	{
		switch ($this->rol_id) {
			case config('constantes.ROL_SOLICITANTE_PRODUCTOR'):
			case config('constantes.ROL_COORDINADOR'):

			case config('constantes.ROL_PERSONAL_GERENCIA'):
			case config('constantes.ROL_PERSONAL_PRODUCCION'):
			case config('constantes.ROL_PERSONAL_CALIDAD'):
			case config('constantes.ROL_PERSONAL_MANTENCION'):
			case config('constantes.ROL_PERSONAL_ROMANA'):

			case config('constantes.ROL_ADMINISTRADOR_IT'):
				return [													// Admin-IT y Coordinador (tradicional) opera en ambas regiones
					config('constantes.REGION_X'),
					config('constantes.REGION_XII'),
				];

			case config('constantes.ROL_SOLICITANTE_PLANTA'):
				return [config('constantes.REGION_X')];						// Solicitantes siempre operan solo X

			case config('constantes.ROL_SOLICITANTE_PLANTA_XII'):			// Solicitantes Planta de REGION XII
			case config('constantes.ROL_COORDINADOR_XII'):					// y Coordinador XII siempre operan solo XII
				return [config('constantes.REGION_XII')];

			default:
				return [];
		}
	}

	/**
	 * Rótulos de las regiones operativas del usuario, usado en perfil de usuario
	 */
	public function getRegionesOperativasNombresAttribute(): string
	{
		$ids = $this->regiones_operativas_ids;

		if (empty($ids)) {
			return '—';
		}

		return \App\Models\Region::whereIn('id', $ids)
			->orderBy('id')
			->pluck('nombre')
			->implode(', ');
	}


    /**
     * Cada vez que se ejecute el flujo de reset (ya sea con el broker o con el trait),
     * Laravel llamará a notificación CustomResetPassword en lugar de usar la nativa.
     *
     * @return string
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    /**
     * Relaciones Eloquent de Usuario con otras tablas
     *
     * @var array
     */
    public function rol()
    {
        return $this->belongsTo(Catalogo::class); // Un usuario tiene un rol a través campo users.rol_id = catalogos.id
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class); // Un usuario con rol='solicitante productor' trabaja en una empresa mediante users.empresa_id = empresas.id
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class); // Un usuario con rol='solicitante planta' trabaja en una sucursal mediante users.sucursal_id = sucursal.id
    }

    public function comuna()
    {
        return $this->belongsTo(Comuna::class); // Una usuario posee dirección en una comuna a través del campo comuna_id = id
    }

    /**
     * Solicitudes creadas por el usuario
     */
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'usuario_id');
    }

    /**
     * Comentarios de retiro realizados por el usuario.
     */
    public function comentariosRetiros()
    {
        return $this->hasMany(RetiroComentario::class, 'usuario_id');
    }
}
