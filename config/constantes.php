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

	'CATEGORIA_REGION_OPERATIVA'			=> 130,


	// Categorías que NO deben mostrarse en el mantenedor (frontend)
	// Estas categorias no se consideran porque no son configurables por FRONT
	'CATALOGOS_NO_FRONT' => [
								  0,   // No especificado
								  1,   // Rol de Usuario
								 20,   // Tipo de Relación

								 29,   // Tipo de Sucursal
								 31,   // Tipo Empresa

								130,   // Región Operativa
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


];
