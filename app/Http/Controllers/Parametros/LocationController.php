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
			->where('id', '!=', 0)     // El registro neutro no corresponde a una ubicación seleccionable
			->find($idRegion);         // Carga la relación comunas definida en el modelo Region buscando la región por su ID

		return $region?->comunas ?? collect();         // Accede a las comunas de la región si existe y si $region es null retorna una colección vacía (en lugar de lanzar error).
	}

	public function obtenerRegion()
	{
		return Region::orderBy("orden", "asc")
			->where('id', '!=', 0)         // El registro neutro no corresponde a una ubicación seleccionable
			->get();
	}

	public function obtenerRegionOperativa()
	{
		return Region::orderBy("orden", "asc")
			->where('id', '!=', 0)         // El registro neutro no corresponde a una ubicación seleccionable
			->where('operativa', true)     // Lo mismo que obtenerRegion pero estas son las operativas
			->get();
	}
}
