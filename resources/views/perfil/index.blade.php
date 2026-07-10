@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
        <div class="col-md-4">
            <div class="card">
				<div class="card-header bg-primary text-white fs-5">
					Mi Perfil
				</div>

				<div class="card-body">
					<form action="{{ url('/perfil') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="panel-body mx-3">
                            <div class="row">
								<div class="form-group col-12 text-center">
									<img src="{{ asset('uploads/avatar/' . ($user->avatar ? $user->avatar . '_medium.jpg' : 'default_medium.jpg')) }}"
										alt="Avatar Mediano"
										class="rounded-circle img-fluid"
										style="max-width: 250px; height: auto; display: block; margin: auto;">
										 <h4 class="mt-3">{{ ucfirst($user->nombre_usuario) }} {{ ucfirst($user->apellidos_usuario) }}</h4>
								</div>
								<hr>
								<div class="form-group col-12">
									<label for="avatar">Subir Foto de Perfil</label>
									<input type="file" name="avatar" id="avatar" required accept="image/*" class="form-control">
								</div>
							</div>
						</div>
						<div class="text-center mt-3">
							<button type="submit" class="btn btn-primary w-100 w-md-auto">
								<i class="fa fa-refresh"></i> Actualizar Foto de Perfil
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-8">
            <div class="card">
				<div class="card-header bg-primary text-white fs-5">
					Mi Datos Personales
				</div>

				<div class="card-body">
					@include('includes.alertas-sistema')

					<div class="form-group mb-3">
						<div class="d-grid gap-2 d-md-flex justify-content-md-start">
							<button type="button" class="btn btn-secondary" onclick="window.location.href='/perfil/password/{{Crypt::encrypt(Auth::user()->id)}}'">
								<i class="fa fa-key"></i> Actualizar Contraseña
							</button>

							@if(	$operationalParameter->allow_profile_editing ||
									Auth::user()->rol_id  == config('constantes.ROL_ADMINISTRADOR_IT') || Auth::user()->rol_id  == config('constantes.ROL_COORDINADOR') )
								<button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('perfil.edit', ['perfil' => Crypt::encrypt(Auth::user()->id)]) }}'">
									<i class="fa fa-edit"></i> Modificar Perfil
								</button>
							@endif
						</div>
					</div>

					<div class="form-group mb-3">
						<h5>Datos Personales</h5>
						<table class="table table-bordered table-striped">
							<tbody>
								<tr><th>Nombre</th><td colspan="2">{{ ucfirst($user->nombre_usuario) }}</td></tr>
								<tr><th>Apellidos</th><td colspan="2">{{ ucfirst($user->apellidos_usuario) }}</td></tr>
								<tr><th>Rut</th><td colspan="2">{{ $user->rut_usuario }}</td></tr>
								<tr><th>Correo Electrónico</th><td colspan="2">{{ $user->email }}</td></tr>
								<tr><th>Teléfono Móvil</th><td colspan="2">{{ $user->telefono }}</td></tr>
								<tr><th>Rol de Usuario</th><td colspan="2">{{ $user->rol->nombre }}</td></tr>
								<tr><th>Empresa o Sucursal</th>
									<td colspan="2">
										@switch($user->rol_id)
											@case(config('constantes.ROL_SOLICITANTE_PLANTA'))
												{{ $user->sucursal->nombre_sucursal ?? '-' }}
												@break

											@case(config('constantes.ROL_SOLICITANTE_PLANTA_XII'))
												{{ $user->sucursal->nombre_sucursal ?? '-' }}
												@break

											@case(config('constantes.ROL_SOLICITANTE_PRODUCTOR'))
												{{ $user->empresa->razon_social ?? '-' }}
												@break

											@default
												{{ 'NA' }} {{-- No Aplica porque el rol del usuario no coincide con solicitante en ningún caso --}}
										@endswitch
									</td>
								</tr>

								<tr>
									<th>Regiones operativas</th>
									<td colspan="2">{{ $user->regiones_operativas_nombres }}</td>
								</tr>

							</tbody>
						</table>
					</div>

					<div class="form-group mb-3">
						<h5>Dirección</h5>
						<table class="table table-bordered table-striped">
							<tbody>
								<tr><th>Dirección</th><td colspan="2">{{ $user->direccion }}</td></tr>
								<tr><th>Comuna</th><td colspan="2">{{ $user->comuna->nombre }}</td></tr>
								<tr><th>Región</th><td colspan="2">{{ $user->comuna->region->nombre }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
