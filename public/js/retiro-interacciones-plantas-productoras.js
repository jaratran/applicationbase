// Carga listado de Productoras para asociar al Retiro a partir de la Planta del Usuario
function cargarProductorasDesdePlanta(plantaId) {
    $.get("/planta/" + plantaId + "/productoras-vinculadas", function(data) {
        var seleccionada = $("#empresa_actual").val();
        var opciones = '<option value="">Seleccione Productora Asociada</option>';
        for (var i = 0; i < data.length; i++) {
            opciones += '<option value="' + data[i]['id'] + '"';
            if (seleccionada == data[i]['id']) {
                opciones += ' selected';
            }
            opciones += '>' + data[i]['data'] + '</option>';
        }
        $("#empresa_retiro").html(opciones);
    });
}
// Carga listado de Plantas para asociar al Retiro a partir de la Productora del Usuario
function cargarPlantasDesdeProductora(productoraId) {
    $.get("/productora/" + productoraId + "/plantas-vinculadas", function(data) {
        var seleccionada = $("#sucursal_actual").val();
        var opciones = '<option value="">Seleccione Planta Asociada</option>';
        for (var i = 0; i < data.length; i++) {
            opciones += '<option value="' + data[i]['id'] + '"';
            if (seleccionada == data[i]['id']) {
                opciones += ' selected';
			}
			opciones+=' data-region="' + data[i]['region_operativa_id'] + '"'; // Traspasamos region_operativa_id al frontend
			opciones+='>' + data[i]['data'] + '</option>';
        }
        $("#sucursal_retiro").html(opciones);
    });
}
// Carga listado de todas las Productoras para asociar al Retiro para usuario AdminIT y Coordinador
function cargarProductoras() {
    $.get("/empresas/tipo/" + TIPO_EMPRESA_PRODUCTORA ,function(data) {  // tipo_empresa = 32 -> Id de Productora de Materia Prima en Catalogos
        var getEmp = $("#empresa_actual").val();
        var empresaRetiro = '<option value="">Seleccione Empresa de Retiro</option>';
        for (var i = 0;i<data.length;i++) {
            empresaRetiro+='<option value="'+data[i]['id']+'"';
            if (getEmp==data[i]['id']) {
                empresaRetiro+=" selected";
            }
            empresaRetiro+='>'+data[i]['razon_social']+'</option>';
        }
        $("#empresa_retiro").html(empresaRetiro);
    });
}
// Carga listado de todas las Plantas para asociar al Retiro para usuario AdminIT y Coordinador
function cargarPlantas() {
	$('#alerta-region-xii').addClass('d-none');							// Oculto el Alert, dado que vamos a recargar plantas

	$.get("/sucursales/tipo/" + TIPO_SUCURSAL_PLANTA, function (data) {  // tipo_sucursal = 30 -> Id de Planta de Proceso en Catalogos
        var getSuc = $("#sucursal_actual").val();
        var sucursalRetiro = '<option value="">Seleccione Planta de Proceso</option>';
        for (var i = 0;i<data.length;i++) {
            sucursalRetiro+='<option value="'+data[i]['id']+'"';
            if (getSuc==data[i]['id']) {
                sucursalRetiro+=" selected";
            }
			sucursalRetiro+=' data-region="' + data[i]['region_operativa_id'] + '"'; // Traspasamos region_operativa_id al frontend
			sucursalRetiro+='>'+data[i]['nombre_sucursal']+'</option>';
        }
		$("#sucursal_retiro").html(sucursalRetiro);

		// Aprovechamos de evaluar de inmediato si debemos desplegar BANDERA de ADVERTENCIA : SUCURSAL REGION XII
		const rol            = window.contextUsuario?.rol_id;
		const regionSucursal = $('#sucursal_retiro option:selected').data('region');
		toggleAlertaRegionXII(rol, regionSucursal);
    });
}

// Inicializar listado de Empresas
const rolUsuario = window.contextUsuario?.rol_id;
switch(rolUsuario) {
    case ROL_SOLICITANTE_PRODUCTOR:
        // Usuario es de tipo Productor → cargar Plantas vinculadas a su Empresa
        const productoraId = window.contextUsuario?.empresa_id; // Evita que se escape un null como string
        if (productoraId) {
            cargarPlantasDesdeProductora(productoraId);
        }
        break;

    case ROL_SOLICITANTE_PLANTA:
    case ROL_SOLICITANTE_PLANTA_XII:
        // Usuario es de tipo Planta → cargar Productoras vinculadas a su Planta
        const plantaId     = window.contextUsuario?.sucursal_id; // Evita que se escape un null como string
        if (plantaId) {
            cargarProductorasDesdePlanta(plantaId);
        }
        break;

	default:
        // Otro Rol: AdminIT (X/XII), Coordinador (X/XII) ó Coordinador XII (sólo XII) → cargar todos los datos
        const $select2empresa = $('#empresa_retiro');
        const $select2sucursal = $('#sucursal_retiro');
        const $boton = $('#btn-fijar');

        let fijado = null; // 'empresa' o 'sucursal'

        // Detectar cambios iniciales
        $select2empresa.on('change', function () {
            $('#empresa_retiro_hidden').val($select2empresa.val()); // Copiamos la empresa seleccionada para enviarla al back

            if(fijado === null){
                if ($select2empresa.val()) {
                    $('#zona-boton')
                        .removeClass('justify-content-end justify-content-center')
                        .addClass('justify-content-start');
                    $boton.html('<i class="fas fa-thumbtack me-1"></i> Fijar Productora')
                        .removeClass('d-none')
                        .removeClass('btn-secondary')
                        .addClass('btn-primary')
                        .data('fijar', 'empresa');
                    cargarPlantas(); // Limpio el listado de Plantas
                }
                else{
                    $boton.addClass('d-none');
                }
            }
        });
        $select2sucursal.on('change', function () {
            $('#sucursal_retiro_hidden').val($select2sucursal.val()); // Copiamos la sucursal seleccionada para enviarla al back

            if (fijado === null) {
                if ($select2sucursal.val()) {
                    $('#zona-boton')
                        .removeClass('justify-content-start justify-content-center')
                        .addClass('justify-content-end');
                    $boton.html('<i class="fas fa-thumbtack me-1"></i> Fijar Planta')
                        .removeClass('d-none')
                        .removeClass('btn-secondary')
                        .addClass('btn-primary')
                        .data('fijar', 'sucursal');
                    cargarProductoras(); // Limpio el listado de Productoras
                }
                else{
                    $boton.addClass('d-none');
                }
            }
        });
        // Botón fijar/liberar
        $boton.on('click', function () {
            const accion = $boton.data('fijar');

            switch (accion) {
                case 'empresa': // Fijamos empresa, cargamos sucursales
                    fijado = 'empresa';
                    $select2empresa.prop('disabled', true);

                    cargarPlantasDesdeProductora($select2empresa.val());
                    $boton.html('<i class="fas fa-lock-open me-1"></i> Liberar Productora')
                        .removeClass('btn-primary')
                        .addClass('btn-secondary')
                        .data('fijar', 'liberar');
                    break;

                case 'sucursal': // Fijamos sucursal, cargamos empresas
                    fijado = 'sucursal';
                    $select2sucursal.prop('disabled', true);

                    cargarProductorasDesdePlanta($select2sucursal.val());
                    $boton.html('<i class="fas fa-lock-open me-1"></i> Liberar Planta')
                        .removeClass('btn-primary')
                        .addClass('btn-secondary')
                        .data('fijar', 'liberar');
                    break;

                case 'liberar':
                    switch (fijado) {
                        case 'empresa':
                            $select2empresa.prop('disabled', false);
                            $('#zona-boton')
                                .removeClass('justify-content-end justify-content-center')
                                .addClass('justify-content-start');
                            $boton.html('<i class="fas fa-thumbtack me-1"></i> Fijar Productora')
                                .removeClass('d-none')
                                .removeClass('btn-secondary')
                                .addClass('btn-primary')
                                .data('fijar', 'empresa');
                            cargarPlantas();
                            break;

                        case 'sucursal':
                            $select2sucursal.prop('disabled', false);
                            $('#zona-boton')
                                .removeClass('justify-content-start justify-content-center')
                                .addClass('justify-content-end');
                            $boton.html('<i class="fas fa-thumbtack me-1"></i> Fijar Planta')
                                .removeClass('d-none')
                                .removeClass('btn-secondary')
                                .addClass('btn-primary')
                                .data('fijar', 'sucursal');
                            cargarProductoras();
                            break;
                    }
                    fijado = null;
                    break;
            }
        });

		// Carga inicial
        cargarProductoras();
		cargarPlantas();
}

// Muestra u oculta la alerta de sucursal Región XII según rol y región.
function toggleAlertaRegionXII(rol, regionSucursal) {
	if ( (rol === ROL_SOLICITANTE_PRODUCTOR || rol === ROL_COORDINADOR || rol === ROL_ADMINISTRADOR_IT) && regionSucursal === REGION_XII	) {
		$('#alerta-region-xii').removeClass('d-none');
	} else {
		$('#alerta-region-xii').addClass('d-none');
	}
}

// Listener de cambio en sucursal - Para Presentar el Alert de Sucursal de XII Región
$('#sucursal_retiro').on('change', function () {
	const rol = window.contextUsuario?.rol_id;
    const regionSucursal = $('#sucursal_retiro option:selected').data('region');

	// Despliegue de BANDERA de ADVERTENCIA de SUCURSAL REGION XII
	toggleAlertaRegionXII(rol, regionSucursal);

	// RESGUARDAMOS REGION OPERATIVA EN VARIABLE WINDOW y ELEMENTO HIDDER para PERSISTENCIA en REBOTE
    window.regionOperativa = regionSucursal;
	const inputRegionOperativa = document.getElementById('region_operativa');
	if (inputRegionOperativa) {
		inputRegionOperativa.value = window.regionOperativa;
	}

	// Esta invocación invoca el modulo que encapsula la inicializacióin de la sección de solicitud
	// Este modulo también se invoca en solicitud-retiro-form-controls.js
	// en el listener document.addEventListener('DOMContentLoaded', inicializarSolicitudRetiro);
	inicializarSolicitudRetiro();

	// ⚠️ DEPENDENCIA:
	// Este módulo invoca inicializarPlanificacionRetiro()
	// definida en planificacion-retiro-form-controls.js
	// SOLO en contexto de planificación (creación manual de solicitud y planificación) ...
	if (window.contextoVista.esPlanificacion) {
		inicializarPlanificacionRetiro();				// ... volvemos a inicializar elementos tal cual al principio (cuando carga el DOM)
	}
});
