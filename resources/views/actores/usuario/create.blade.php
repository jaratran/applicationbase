@extends('layouts.app')

@section('head-scripts')
    <!-- Select2 CSS (mejora visual y funcional de elementos <select>) -->
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap.css') }}">
    <!-- Personalizar select2 en cuanto a altura de selector y color de opción destacada -->
    <link rel="stylesheet" href="{{ asset('css/personalizaciones-select2.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white fs-5">
                    Crear Usuario
                </div>
                <form id="form_crear_usuario" action="{{ url('actores/usuario') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        @include('includes.alertas-sistema')

                        <div class="bg-light border rounded p-3 mb-4">
                            <h5>Datos Personales</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="rut_usuario">Rut</label>
                                    <input type="text" class="form-control rut" name="rut_usuario" id="rut_usuario" placeholder="Ej: 12.345.678-9" required maxlength="12" autofocus>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="nombre_usuario">Nombres</label>
                                    <input type="text" class="form-control" name="nombre_usuario" id="nombre_usuario" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="apellidos_usuario">Apellidos</label>
                                    <input type="text" class="form-control" name="apellidos_usuario" id="apellidos_usuario" required>
                                </div>
                            </div>

							<div class="row">
								<div class="form-group col-md-4 mt-4">
									<label for="email">Correo Electrónico</label>
									<input type="email" class="form-control" name="email" id="email" required>
								</div>
								<div class="form-group col-md-4 mt-4">
									<label for="telefono_usuario">Teléfono</label>
									<input type="text" class="form-control" name="telefono" id="telefono" required placeholder="+ 569 11 111 111">
								</div>
								<div class="form-group col-md-4 mt-4">
									<label>Avatar</label>
									<input type="file" class="form-control" name="avatar">
								</div>
							</div>

							<div class="row align-items-end">
								<div class="form-group col-md-4 mt-4">
									<label>Rol de Usuario</label>
									<select class="form-control select2" id="rol_id" name="rol_id" required>
										<option value="">Seleccione Rol de Usuario</option>
										@foreach($roles as $role)
											<option value="{{ $role->id }}" {{ old('rol_id') == $role->id ? 'selected' : '' }}>{{ $role->nombre }}</option>
										@endforeach
									</select>
								</div>

								<!-- Botón para Asignar o Liberar Categoría de Agrupadores -->
								<div class="form-group col-md-4 d-none" id="div_asignar_liberar" name="div_asignar_liberar">
									<button id="btn_asignar_liberar" name="btn_asignar_liberar"
											type="button" class="btn btn-primary"
											data-modo="" data-target="">
									</button>
								</div>

								<div class="form-group col-md-4 mt-4 d-none" id="div_empresa_usuario">
									<label>Productora de Materia Prima</label>
									<select class="form-control select2" id="empresa_id" name="empresa_id">
										<option value="">Cargando Productoras de Materia Prima ...</option>
									</select>
								</div>
								<div class="form-group col-md-4 mt-4 d-none" id="div_sucursal_usuario">
									<label>Planta de Proceso</label>
									<select class="form-control select2" id="sucursal_id" name="sucursal_id">
										<option value="">Cargando Plantas de Proceso ...</option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-md-4 mt-4">
									<label for="region_operativa_codigo">Región Operativa</label>
									<input type="text" class="form-control" id="region_operativa_codigo" disabled>
								</div>
                            </div>
                        </div>

						<div class="bg-light border rounded p-3">
                            <h5>Dirección</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Región</label>
                                    <select class="form-control select2" id="region_usuario" name="region_usuario" required>
                                        <option value="">Cargando Regiones ...</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Comuna</label>
                                    <select class="form-control select2" id="comuna_usuario" name="comuna" required>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Dirección</label>
                                    <input class="form-control" name="direccion" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary my-2"><i class="fa fa-edit"></i> Crear Usuario</button>
                        <button type="button" class="btn btn-secondary my-2" onclick="window.location.href='{{ url('actores/usuario') }}'">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de advertencia -->
<div class="modal fade" id="modal-validacion-rol" tabindex="-1" aria-labelledby="modalValidacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalValidacionLabel">Asignación Requerida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body text-center" id="mensaje-validacion-rol">
                <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 4rem;"></i>

                <p id="mensajePrincipal" class="fs-5"></p>
                <p id="mensajeSecundario" class="text-muted small mb-0"></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-check-circle me-2"></i> Entendido
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('endbody-scripts')
    <!-- Select2 JavaScript (extensión de elementos <select> con búsqueda, multiselección, etc.) -->
    <script src="{{ asset('js/select2.full.js') }}"></script>
    @include('includes.constantes-js-catalogo')

    <script>
        window.onload = function() {
            $(".select2").select2({theme: "bootstrap"});
        }
    </script>

    <!-- Carga de Select2 varios -->
    <!-- Selección de Productora y Planta en caso de ser AdminIT o Coordinador y estar creando usuarios de todo tipo -->
    <script>
		function empresaUsuarioRol(rolId) {
			if (rolId == ROL_SOLICITANTE_PRODUCTOR) {
				$.get("/empresas/tipo/" + TIPO_EMPRESA_PRODUCTORA + "/por-rol", {
						rol_id: rolId
					}, function (data) {  // tipo_empresa = 32 -> Id de Productora de Materia Prima en Catalogos
						var getEmp = $("#empresa_actual").val();
						var empresaUsuario = '<option value="">Seleccione Productora de Materia Prima</option>';

						for (var i = 0; i < data.length; i++) {
							empresaUsuario += '<option value="' + data[i]['id'] + '"';
							if (getEmp == data[i]['id']) {
								empresaUsuario += " selected";
							}
							empresaUsuario += '>' + data[i]['razon_social'] + '</option>';
						}
						$("#empresa_id").html(empresaUsuario);
					}
				);
			}
		}
		function sucursalUsuarioRol(rolId) {
			if ((rolId == ROL_SOLICITANTE_PLANTA) || (rolId == ROL_SOLICITANTE_PLANTA_XII)) {
				$.get("/sucursales/tipo/" + TIPO_SUCURSAL_PLANTA + "/por-rol", {
						rol_id: rolId
					}, function (data) {  // tipo_sucursal = 30 -> Id de Planta de Proceso en Catalogos
						var getSuc = $("#sucursal_actual").val();
						var sucursalUsuario = '<option value="">Seleccione Planta de Proceso</option>';

						for (var i = 0; i < data.length; i++) {
							sucursalUsuario += '<option value="' + data[i]['id'] + '"';
							if (getSuc == data[i]['id']) {
								sucursalUsuario += " selected";
							}
							sucursalUsuario += '>' + data[i]['nombre_sucursal'] + '</option>';
						}
						$("#sucursal_id").html(sucursalUsuario);
					}
				);
			}
		}

		function direccionComuna(idRegion) {
			$.get("/parametros/comuna",{idRegion:idRegion},function(data) {
				var comunaUsuario = '<option value="">Seleccione Comuna</option>';

				for (var i = 0;i<data.length;i++) {
					comunaUsuario+='<option value="'+data[i]['id']+'"';
					comunaUsuario+='>'+data[i]['nombre']+'</option>';
				}
				$("#comuna_usuario").html(comunaUsuario);
			});
		}
		function direccionRegion() {
			$.get("/parametros/region",{},function(data) {
				var regionUsuario = '<option value="">Seleccione Región</option>';
				for (var i = 0;i<data.length;i++) {
					regionUsuario+='<option value="'+data[i]['id']+'"';
					regionUsuario+='>'+data[i]['nombre']+'</option>';
				}
				$("#region_usuario").html(regionUsuario);

				// Cargar comunas al cambiar de región
				$("#region_usuario").on("change", function () {
					let nuevaRegion = $(this).val();
					direccionComuna(nuevaRegion);
				});
			});
		}

		$(document).ready(function () {
            const $selectRolUsuario = $('#rol_id');
            const $selectSucursalUsuario = $('#sucursal_id');
            const $selectEmpresaUsuario = $('#empresa_id');

            const $divAsignarLiberar = $('#div_asignar_liberar');
            const $divSucursalUsuario = $('#div_sucursal_usuario');
            const $divEmpresaUsuario = $('#div_empresa_usuario');

            const $btnAsignarLiberar = $('#btn_asignar_liberar');

            // Evento para el Selector de Rol de Usuario -> Muestra u oculta los selectores de Planta/Empresa según el rol seleccionado
            $selectRolUsuario.on('change', function () {
                const getRol = $selectRolUsuario.val();
				const codigo = REGION_OPERATIVA_POR_ROL[getRol] ?? 'NI';

				$('#region_operativa_codigo').val(codigo);

                switch( parseInt(getRol) ) {
                    case ROL_SOLICITANTE_PLANTA:
                    case ROL_SOLICITANTE_PLANTA_XII:
                        $btnAsignarLiberar.text('Asignar Planta de Proceso');
                        $btnAsignarLiberar.attr('data-modo', 'asignar');
                        $btnAsignarLiberar.attr('data-target', 'planta');
                        $divAsignarLiberar.removeClass('d-none');
                        $divEmpresaUsuario.addClass('d-none');
                        break;

                    case ROL_SOLICITANTE_PRODUCTOR:
                        $btnAsignarLiberar.text('Asignar Productora de Materia Prima');
                        $btnAsignarLiberar.attr('data-modo', 'asignar');
                        $btnAsignarLiberar.attr('data-target', 'productora');
                        $divAsignarLiberar.removeClass('d-none');
                        $divSucursalUsuario.addClass('d-none');
                        break;

                    default:
                        $divAsignarLiberar.addClass('d-none');
                        $divSucursalUsuario.addClass('d-none');
                        $divEmpresaUsuario.addClass('d-none');
                }
            });

            // Evento para el Botón Asignar/Liberar -> Cambia entre asignar y liberar según el modo actual
            $btnAsignarLiberar.on('click', function () {
                const btnModo = $btnAsignarLiberar.attr('data-modo');
                const btnTarget = $btnAsignarLiberar.attr('data-target');

                // ASIGNAR -> Deshabilitamos Selector Rol_Usuario, Aparecemos Selector de Productora/Planta y Botón a modo "Liberar"
                if ( btnModo === 'asignar') {
                    $selectRolUsuario.prop('disabled', true);

                    if ( btnTarget === 'planta') {
                        $selectSucursalUsuario.prop('disabled', false);
                        $divSucursalUsuario.removeClass('d-none');

                        if ( $selectSucursalUsuario.val() !== "" ) {
                            $btnAsignarLiberar.text('Cancelar Asignación de Planta');
                        }
                        else {
                            $btnAsignarLiberar.text('Liberar Selector de Rol de Usuario');
                        }
                    }
                    if ( btnTarget === 'productora') {
                        $selectEmpresaUsuario.prop('disabled', false);
                        $divEmpresaUsuario.removeClass('d-none');

                        if ( $selectEmpresaUsuario.val() !== "" ) {
                            $btnAsignarLiberar.text('Cancelar Asignación de Productora');
                        }
                        else {
                            $btnAsignarLiberar.text('Liberar Selector de Rol de Usuario');
                        }
                    }

                    $btnAsignarLiberar.attr('data-modo', 'liberar');

					empresaUsuarioRol($('#rol_id').val());
					sucursalUsuarioRol($('#rol_id').val());
				}

                // LIBERAR -> Habilitamos Selector Rol_Usuario, Escondemos Selector de Productora/Planta y Botón a modo "Asignar" ...
                if ( btnModo === 'liberar') {
                    $selectRolUsuario.prop('disabled', false);

                    if ( btnTarget === 'planta') {
                        $divSucursalUsuario.addClass('d-none');
                        $selectSucursalUsuario.prop('disabled', true);

                        $btnAsignarLiberar.text('Asignar Planta de Proceso');
                    }
                    if ( btnTarget === 'productora') {
                        $divEmpresaUsuario.addClass('d-none');
                        $selectEmpresaUsuario.prop('disabled', true);

                        $btnAsignarLiberar.text('Asignar Productora de Materia Prima');
                    }

                    $btnAsignarLiberar.attr('data-modo', 'asignar');
                }
            });

            // Evento para el Selector de Planta de Proceso -> Actualiza el texto del botón según la selección
            $selectSucursalUsuario.on('change', function () {
                if ( $selectSucursalUsuario.val() !== "" ) {
                    $btnAsignarLiberar.text('Cancelar Asignación de Planta');
                }
                else {
                    $btnAsignarLiberar.text('Liberar Selector de Rol de Usuario');
                }
            });

            // Evento para el Selector de Productora de Materia Prima -> Actualiza el texto del botón según la selección
            $selectEmpresaUsuario.on('change', function () {
                if ( $selectEmpresaUsuario.val() !== "" ) {
                    $btnAsignarLiberar.text('Cancelar Asignación de Productora');
                }
                else {
                    $btnAsignarLiberar.text('Liberar Selector de Rol de Usuario');
                }
            });

            // Evento para el Submit del Formulario -> Validación de Rol de Usuario
            $('#form_crear_usuario').on('submit', function(e) {
                // 🔁 Controlar doble submit - Interceptar submit cuando proviene de una acción de "enter" o del botón "Ir" del teclado.
                if (this.enviado) {
                    console.log("⛔ Doble submit detectado, se detiene.");
                    e.preventDefault();
                    return;
                }

                const $selectRolUsuario = $('#rol_id');
                const rolSeleccionado = parseInt($selectRolUsuario.val());

                const plantaSeleccionada = $('#sucursal_id').val();
                const productoraSeleccionada = $('#empresa_id').val();
                const btnModo = $btnAsignarLiberar.attr('data-modo');

                let activarModal = false;
                let mensajePrincipal = "";
                let mensajeSecundario = "";

                if ((rolSeleccionado === ROL_SOLICITANTE_PLANTA) && ((btnModo === 'asignar') || !plantaSeleccionada)) {
                    activarModal = true;
                    mensajePrincipal = "Rol seleccionado: Solicitante Planta";
                    mensajeSecundario = "Debe seleccionar una Planta de Proceso antes de guardar.";
                }
                if (rolSeleccionado === ROL_SOLICITANTE_PRODUCTOR && ((btnModo === 'asignar') || !productoraSeleccionada)) {
                    activarModal = true;
                    mensajePrincipal = "Rol seleccionado: Solicitante Productora";
                    mensajeSecundario = "Debe seleccionar una Productora de Materia Prima antes de guardar.";
                }

                if (activarModal) {
                    e.preventDefault(); // Detiene el submit
                    $('#mensajePrincipal').text(mensajePrincipal);
                    $('#mensajeSecundario').text(mensajeSecundario);
                    const modal = new bootstrap.Modal(document.getElementById('modal-validacion-rol'));
                    modal.show();
                }

                // Aseguramos que el selector de rol esté habilitado al enviar el formulario
                $selectRolUsuario.prop('disabled', false);

                // Deshabilita el botón para evitar segundo click
                const $btnSubmit = $('#form_crear_usuario button[type="submit"]');
                if ($btnSubmit.length) {
                    // Deshabilita el botón para evitar segundo click
                    $btnSubmit.prop('disabled', true);
                    $btnSubmit.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
                }

                // 🧷 Flag para evitar siguiente submit
                this.enviado = true;
            });

			empresaUsuarioRol($('#rol_id').val());
			sucursalUsuarioRol($('#rol_id').val());

			direccionRegion();
		});

        // Limpiar el foco antes de ocultar
        document.getElementById('modal-validacion-rol').addEventListener('hide.bs.modal', function() {
            document.activeElement?.blur();
        });
    </script>
@endsection
