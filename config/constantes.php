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

	'CATEGORIA_REGION_OPERATIVA'			=> 130,
	'CATEGORIA_TIPO_TRANSPORTE'				=> 140,


	// Categorías que NO deben mostrarse en el mantenedor (frontend)
	// Estas categorias no se consideran porque no son configurables por FRONT
	'CATALOGOS_NO_FRONT' => [
								  0,   // No especificado
								  1,   // Rol de Usuario
								 20,   // Tipo de Relación

								 29,   // Tipo de Sucursal
								 31,   // Tipo Empresa

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
    'ENTREGADA_LA_PORTADA'          => 206,

	/*
    |--------------------------------------------------------------------------
    | Regiones operativas
    |--------------------------------------------------------------------------
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
    | Estos valores identifican la etapa vigente dentro del flujo de un retiro.
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


];
