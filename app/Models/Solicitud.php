<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Maquila;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'usuario_id',
        'maquila_id',
    ];

	protected $appends = [
		'region_operativa_id',
		'region_operativa_codigo',
	];

	/**
	 * Método getRegionOperativaCodigoAttribute (->region_operativa_codigo) en Camión para obtener el código de la región operativa fácilmente.
	 *
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

		return $map[$this->region_operativa_id] ?? 'NA';
	}

    /**
     * Accessor “región operativa” de una Solicitud.
	 *
	 * La tabla de solicitudes no tiene región propia.
	 * Pero se puede derivar de forma determinística desde:
	 * 	Solicitud
	 * 		→ Maquila
   	 * 			→ Sucursal
	 * 				→ Comuna
	 * 					→ region_id
	 *
     */
	public function getRegionOperativaIdAttribute(): int
	{
		return $this->maquila
			->sucursal
			->comuna
			->region_id
			?? config('constantes.REGION_NI');
	}

    /**
     * Scope local de Eloquent para filtrado según el tipo de solicitante en el modelo Solicitud.
     * Encapsula la lógica de visibilidad de solicitudes según el rol del usuario solicitante.
     * Se usa en métodos index de controladores SolicitudesRetiroController y PlanificacionesRetiroController
     * para evitar repetición de lógica condicional.
     */
    public function scopeVisiblesSegunRol($query, $esSolicitantePlanta, $esSolicitanteProductor)
    {
        $usuarioId = Auth::id();
        $empresaUsuario = Auth::user()->empresa_id;

        return $query
            ->when($esSolicitantePlanta, function ($query) use ($usuarioId) {                               // 👈 Si es solicitante PLANTA
                $query->where('usuario_id', $usuarioId);                                                    //      Filtra las propias (por usuario de sesión)
            })

            ->when($esSolicitanteProductor, function ($query) use ($usuarioId, $empresaUsuario) {                   // 👈 Si es solicitante PRODUCTOR
                $query->where(function ($q) use ($usuarioId, $empresaUsuario) {
                    $q->where('usuario_id', $usuarioId)                                                             // 👈 Extrae las propias
                    ->orWhereHas('maquila', function ($subq) use ($empresaUsuario) {
                        $subq->where('empresa_id', $empresaUsuario);                                                // 👈 Y las asociadas a su empresa
                    });
                });
            });
    }

	/**
	 * Limita las solicitudes a las regiones operativas del usuario en sesión.
	 */
	public function scopePorRegionesOperativas($query, array $regionesOperativasIds)
	{
		// 🧠 Seguridad defensiva: si no hay regiones, no devolvemos nada
		if (empty($regionesOperativasIds)) {
			return $query->whereRaw('1 = 0');
		}

		/*
		* La región operativa de la solicitud se infiere desde la comuna
		* de la sucursal de maquila (fuente única y consistente).
		*/
		return $query->whereHas('maquila.sucursal.comuna', function ($q) use ($regionesOperativasIds) {
			$q->whereIn('region_id', $regionesOperativasIds);
		});
	}

    /**
     * Usuario que creó la solicitud
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Maquila asociada a la solicitud
     */
    public function maquila()
    {
        return $this->belongsTo(Maquila::class, 'maquila_id');
    }

    /**
     * Retiros asociados a la solicitud
     */
    public function retiros()
    {
        return $this->hasMany(Retiro::class, 'solicitud_id');
    }

}
