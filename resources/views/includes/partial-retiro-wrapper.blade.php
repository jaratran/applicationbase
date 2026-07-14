@php
	/**
	* Wrapper neutro para un ítem de Retiro.
	*
	* Variables esperadas:
	* - $index           : índice del retiro
	* - $retiro          : modelo Retiro o null
	*
	* NOTA:
	* La región operativa NO se decide aquí.
	* Este wrapper carga ambos parciales y el frontend decide cuál mostrar.
	*/
@endphp

<div class="retiro-item-wrapper" data-index="{{ $index }}">

	{{-- Región X --}}
	<div class="retiro-region retiro-region-x d-none" data-region="{{ config('constantes.REGION_X') }}">
		@include('includes.partial-retiro-item-x-region', [
			'index'           => $index,
			'retiro'          => $retiro,
		])
	</div>

	{{-- Región XII --}}
	<div class="retiro-region retiro-region-xii d-none" data-region="{{ config('constantes.REGION_XII') }}">
		@include('includes.partial-retiro-item-xii-region', [
			'index'           => $index,
			'retiro'          => $retiro,
		])
	</div>

</div>
