<?php

namespace App\Http\Controllers\Parametros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comuna;
use App\Models\Region;
use App\Services\LocationConfiguration;

class LocationController extends Controller
{
	public function obtenerComuna(Request $request, LocationConfiguration $configuration)
	{
		$data = $request->validate([
			'idRegion' => $configuration->regionRule(),
		]);

		return Comuna::query()
			->where('region_id', $data['idRegion'])
			->orderBy('nombre')
			->get(['id', 'nombre']);
	}

	public function obtenerRegion()
	{
		return Region::query()
			->where('id', '<>', 0)
			->orderBy('orden')
			->orderBy('nombre')
			->get(['id', 'nombre']);
	}

	public function obtenerRegionOperativa()
	{
		return Region::query()
			->where('id', '<>', 0)
			->where('operativa', true)
			->orderBy('orden')
			->orderBy('nombre')
			->get(['id', 'nombre']);
	}
}
