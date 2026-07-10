<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comuna;
use App\Models\Region;

class LocationController extends Controller
{
	public function obtenerComuna(Request $request)
	{
		$idRegion = $request->get('idRegion');

		$region = Region::with('comunas')
			->where('id', '!=', 0)     // Ignoramos registro id=0 porque aquel es sólo para permitir crear Planificaciones Vacias
			->find($idRegion);         // Carga la relación comunas definida en el modelo Region buscando la región por su ID

		return $region?->comunas ?? collect();         // Accede a las comunas de la región si existe y si $region es null retorna una colección vacía (en lugar de lanzar error).
	}

	public function obtenerRegion()
	{
		return Region::orderBy("orden", "asc")
			->where('id', '!=', 0)         // Ignoramos registro id=0 porque aquel es sólo para permitir crear Planificaciones Vacias
			->get();
	}

	public function obtenerRegionOperativa()
	{
		return Region::orderBy("orden", "asc")
			->where('id', '!=', 0)         // Ignoramos registro id=0 porque aquel es sólo para permitir crear Planificaciones Vacias
			->where('operativa', true)     // Lo mismo que obtenerRegion pero estas son las operativas
			->get();
	}
}

