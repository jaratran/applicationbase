<?php

return [

    'CATALOGO_NO_ESPECIFICADO'      => 0, // Sirve para inicializar registros en blanco (vacíos)

    /*
    |--------------------------------------------------------------------------
    | Categorías de Catálogo
    |--------------------------------------------------------------------------
    |
    | Identificadores de categorías dentro de la tabla "catalogos" para
    | uso en AJAX, lógica de carga select2, etc.
    |
    */

    'CATEGORIA_ROL_USUARIO'           =>   1,
    'CATEGORIA_TIPO_RELACION'         =>  20,
    'CATEGORIA_ZONA_SUCURSAL'         =>  21,
    'CATEGORIA_TIPO_SUCURSAL'         =>  29,
    'CATEGORIA_TIPO_EMPRESA'          =>  31,
    'CATEGORIA_TIPO_RETIRO'           =>  34,
    'CATEGORIA_TIPO_ESPECIE'          =>  37,
    'CATEGORIA_TIPO_MATERIA_PRIMA'    =>  41,
    'CATEGORIA_TIPO_CAMION'           =>  43,
    'CATEGORIA_GRUPO_TIPO_CAMION'     =>  53,
    'CATEGORIA_CAMBIOS_PLANIFICACION' => 110,

	'CATEGORIA_REGION_OPERATIVA'			=> 130,
	'CATEGORIA_TIPO_TRANSPORTE'				=> 140,
	'CATEGORIA_TIPO_RAMPLA'					=> 150,
	'CATEGORIA_CAPACIDAD_RAMPLA'			=> 160,
	'CATEGORIA_ESTADO_RAMPLA'				=> 170,
	'CATEGORIA_CAMBIOS_PLANIFICACION_XII'	=> 190,


	// Categorías que NO deben mostrarse en el mantenedor (frontend)
	// Estas categorias no se consideran porque no son configurables por FRONT
	'CATALOGOS_NO_FRONT' => [
								  0,   // No especificado
								  1,   // Rol de Usuario
								 20,   // Tipo de Relación

								 29,   // Tipo de Sucursal
								 31,   // Tipo Empresa

								 53,   // Grupo Tipo Camión
								 90,   // Estados de Retiro

								130,   // Región Operativa
								140,   // Tipo Transporte
							],


    /*
    |--------------------------------------------------------------------------
    | Tipos Específicos Usados en Lógica
    |--------------------------------------------------------------------------
    |
    | Valores individuales de catálogo usados para bifurcar lógica,
    | mostrar u ocultar secciones, controlar acceso, etc.
    |
    */

    // Roles de usuarios
    'ROL_SOLICITANTE_PLANTA'        => 61,
    'ROL_SOLICITANTE_PLANTA_XII'    => 71,

	'ROL_SOLICITANTE_PRODUCTOR'     => 62,
    'ROL_PERSONAL_GERENCIA'         => 63,
    'ROL_PERSONAL_PRODUCCION'       => 64,
    'ROL_PERSONAL_CALIDAD'          => 65,

	'ROL_COORDINADOR'               => 66,
    'ROL_COORDINADOR_XII'           => 70,

	'ROL_PERSONAL_MANTENCION'       => 67,
    'ROL_PERSONAL_ROMANA'           => 68,
    'ROL_ADMINISTRADOR_IT'          => 69,

    // Valores usados para bifurcar lógicas
    'TIPO_EMPRESA_PRODUCTORA'       => 32,
    'TIPO_EMPRESA_TRANSPORTISTA'    => 33,

    'TIPO_SUCURSAL_PLANTA'          => 30,

    'TIPO_RETIRO_TOLVA'             => 35,
    'TIPO_RETIRO_BINS'              => 36,

    'TIPO_TRANSPORTE_TIERRA'        => 193,
    'TIPO_TRANSPORTE_BARCAZA'       => 194,
    'TIPO_TRANSPORTE_COMBINADO'     => 195,

    'EN_PLANTA_ORIGEN'              => 200,
    'EN_PUERTO'                     => 201,
    'EN_TRANSITO_MARITIMO'          => 202,
    'EN_TRANSITO_TERRESTRE'         => 203,
    'ARRIBADA_PUERTO_MONTT'         => 204,
    'ASIGNADA_A_CAMIÓN'             => 205,
    'ENTREGADA_LA_PORTADA'          => 206,

	/*
    |--------------------------------------------------------------------------
    | Regiones - Usados para denotar región de operación de Conductores
    |--------------------------------------------------------------------------
    |
    | Agregamos esta sección para implementar la migración de adición del campo región operativa a la tabla de conductores.
    |
    | Referencian el ID del registro en la tabla REGIONES - No se cargan desde la base de datos ni están en el catálogo.
    |
    */
	'REGION_NI'                      => 0,
	'REGION_I'                       => 1,
	'REGION_II'                      => 2,
	'REGION_III'                     => 3,
	'REGION_IV'                      => 4,
	'REGION_V'                       => 5,
	'REGION_VI'                      => 6,
	'REGION_VII'                     => 7,
	'REGION_VIII'                    => 8,
	'REGION_IX'                      => 9,
	'REGION_X'                       => 10,
	'REGION_XI'                      => 11,
	'REGION_XII'                     => 12,
	'REGION_RM'                      => 13,
	'REGION_XIV'                     => 14,
	'REGION_XV'                      => 15,


    /*
    |--------------------------------------------------------------------------
    | Tipo de Operacion de la Solicitud : Retiro o Reposición (Región XII)
    |--------------------------------------------------------------------------
    |
    | Estos valores se usan para identificar la calidad de la operación.
    |
    | En el caso del a Región X sólo serán Retiros.
    | En el caso del a Región XII pueden ser Retiro o Reposición de Bines.
    |
    | No se cargan desde la base de datos ni están en el catálogo.
    |
    */
	'TIPO_OPERACION_RETIRO'          => 0,
	'TIPO_OPERACION_REPOSICION'      => 1,


	/*
    |--------------------------------------------------------------------------
    | Estados de Solicitudes de Retiro
    |--------------------------------------------------------------------------
    |
    | Estos valores se usan para identificar la etapa en el flujo del proceso de un retiro desde solicitud hasta planificación.
    |
    | No se cargan desde la base de datos ni están en el catálogo.
    |
    */
	'CATEGORIA_ESTADOS_RETIRO'      => 90,

    'ESTADO_RETIRO_ESPERANDO'       => 91,
    'ESTADO_RETIRO_COMENTADO'       => 92,
    'ESTADO_RETIRO_ACEPTADO'        => 93,
    'ESTADO_RETIRO_PLANIFICADO'     => 94,
    'ESTADO_RETIRO_PROGRAMADO'      => 95,
    'ESTADO_RETIRO_TERMINADO'       => 96,
    'ESTADO_RETIRO_CANCELADO'       => 97,


    /*
    |--------------------------------------------------------------------------
    | Calidad del Retiro en el Programa Diario
    |--------------------------------------------------------------------------
    |
    | Estos valores se usan para identificar la calidad de un retiro dentro de un programas_diarios.
    |
    | No se cargan desde la base de datos ni están en el catálogo.
    |
    */
    'CALIDAD_RETIRO_ORIGINAL'    => 0,
    'CALIDAD_RETIRO_ACTUALIZADO' => 1,
    'CALIDAD_RETIRO_NUEVO'       => 2,

    /*
    |--------------------------------------------------------------------------
    | Estados del Programa Diario
    |--------------------------------------------------------------------------
    |
    | Estos valores se usan en la creación y mantención de programas_diarios.
    |
    | No se cargan desde la base de datos ni están en el catálogo.
    |
    */
    'ESTADO_PROGRAMA_EMITIDO' => 1,

    /*
    |--------------------------------------------------------------------------
    | Valores especiales para acceder a versiones del Programa Diario
    |--------------------------------------------------------------------------
    |
    | No se cargan desde la base de datos ni están en el catálogo.
    |
    */
    'VERSION_TODAS'  => 0,
    'VERSION_ULTIMA' => -1,
    'VERSION_PRIMERA'=> 1,

    /*
    |--------------------------------------------------------------------------
    | Estados de Notificación (Correo / Telegram)
    |--------------------------------------------------------------------------
    |
    | Estos valores se usan en las tablas programas_diarios y programas_diarios_detalle
    | para indicar el estado del proceso de notificación por correo electrónico
    | y por Telegram.
    |
    | No se cargan desde la base de datos ni están en el catálogo.
    |
    */
    'NOTIF_PENDIENTE'        => 0, // No se ha iniciado el proceso de notificación
    'NOTIF_EN_PROCESO'       => 1, // Notificaciones en curso
    'NOTIF_ENVIADO'          => 2, // Todas las notificaciones fueron enviadas correctamente
    'NOTIF_FALLIDO'          => 3, // Una o más notificaciones fallaron durante el proceso
    'NOTIF_SIN_TELEGRAM'     => 4, // Conductor sin chat_id
    'NOTIF_SIN_CAMBIOS'      => 5, // Retiro no presenta cambios que notificar al Conductor por Mensaje Telegram
    'NOTIF_NO_APLICA_REGION' => 6, // Retiro no corresponde a la región en la que se está notificando

];
