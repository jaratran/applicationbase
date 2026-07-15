<script>
/**
 * ╔════════════════════════════════════════════════════════════════════════════╗
 * ║ FUNCIÓN + CONSTANTES DE CATÁLOGO INYECTADAS DESDE BACKEND (PHP → JS)      ║
 * ║ --------------------------------------------------------------------------║
 * ║ Este bloque contiene la función 'catalogo2select2()' usada en el sistema  ║
 * ║ para cargar dinámicamente selectores <select> con datos desde la tabla    ║
 * ║ 'catalogos', y además define constantes JS inyectadas desde PHP.          ║
 * ║ Estas constantes provienen del archivo: config/constantes.php             ║
 * ║                                                                           ║
 * ║ ⚠️ NO DUPLICAR ESTAS CONSTANTES EN OTROS ARCHIVOS .js                     ║
 * ║ ⚠️ Cualquier cambio de valor debe hacerse exclusivamente en PHP           ║
 * ║ para garantizar una única fuente de verdad en el sistema.                 ║
 * ╚════════════════════════════════════════════════════════════════════════════╝
 */

/**
 * Carga una lista de parámetros desde catalogo list_params en un <select> (apto para create/edit).
 * NO Apto para vistas de create y create manual de solicitud de retiro donde se tienen multiples retiros con doble contexto Región X y Región XII
 *
 * @param {string}		catalogo_id - Categoría de parámetro a cargar dentro de la tabla de Catalogos.
 * @param {string}		idSelect	- ID del <select> destino.
 * @param {string|null}	idHidden	- ID del input hidden con valor preseleccionado (opcional) sino viene asume null.
 */
function catalogo2select2(catalogo_id, idSelect, placeholder = 'Seleccione una opción', idHidden = null) {
    const $select = $(`#${idSelect}`);
    if (!$select.length) {
        console.error(`Elemento #${idSelect} no encontrado`); // Error: el selector desplegable no existe
        return;
    }

    const actual = idHidden ? String(document.getElementById(idHidden)?.value) : null; // actual : opción  pre-seleccionada - Asegurar tipo string

    // console.log('catalogo2select2> idSelect : ', idSelect);
    // console.log('catalogo2select2> idHidden : ', idHidden);
	// console.log('catalogo2select2> actual : ', actual);

    $.get("/parametros/catalogo/" + catalogo_id, function(data) {
        const options = [`<option value="">${placeholder}</option>`];

        data.forEach(e => {
            const id = String(e.id);									// Aseguramos tipo string
            const nombre = String(e.nombre);							// Aseguramos tipo string
            const selected = id === actual ? ' selected' : '';			// Marcar opción si coincide con "actual"
            options.push(`<option value="${id}"${selected}>${nombre}</option>`);
        });

        $select.html(options.join(''));
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        console.error(`Error al cargar lista (${textStatus}):`, errorThrown);
        $select.html('<option value="">Error al cargar opciones</option>');
    });
}

/**
 * Carga una lista de parámetros desde catalogo list_params en un <objeto jQuery select>
 * Apto para vistas de create y create manual de solicitud de retiro donde se tienen multiples retiros con doble contexto Región X y Región XII
 *
 * @param {string}				catalogo_id - Categoría de parámetro a cargar dentro de la tabla de Catalogos.
 * @param {objeto jQuery}		idSelect	- objeto jQuery destino.
 * @param {objeto jQuery|null}	idHidden	- objeto jQuery input hidden con valor preseleccionado (opcional) sino viene asume null.
 */
function catalogo2select2Element(catalogo_id, $select, placeholder = 'Seleccione una opción', $hidden = null) {
	// Verificar que $select es un objeto jQuery válido y no está vacío
    if (!$select || !$select.jquery || !$select.length) {
		console.error('catalogo2select2Element: $select debe ser un objeto jQuery ó está vacío', $select);
        return $select;
    }

    // Validar $hidden si existe
    if ($hidden && (!$hidden.jquery || !$hidden.length)) {
        console.warn('catalogo2select2Element: $hidden inválido, usando null');
        $hidden = null;
    }

	const actual =  $hidden ? String($hidden.val() || '') : null; // actual : opción  pre-seleccionada - Asegurar tipo string

	// console.log('catalogo2select2Element> $select:', $select[0]);
	// console.log('catalogo2select2Element> $hidden:', $hidden[0]);
	// console.log('catalogo2select2Element> actual:', actual);

	$.get("/parametros/catalogo/" + catalogo_id, function(data) {
		// Construir opciones
		const options = [`<option value="">${placeholder}</option>`];

		data.forEach(e => {
			const id = String(e.id);                    // Aseguramos tipo string
			const nombre = String(e.nombre);            // Aseguramos tipo string
			const selected = actual && id === actual ? ' selected' : '';
			options.push(`<option value="${id}"${selected}>${nombre}</option>`);
		});

		// Aplicar las opciones al select
		$select.html(options.join(''));
	})
	.fail(function (jqXHR, textStatus, errorThrown) {
		console.error(`catalogo2select2Element: Error al cargar lista (${textStatus}):`, errorThrown);
		$select.html('<option value="">Error al cargar opciones</option>');
	});

	// Devolver el elemento para posible encadenamiento
	return $select;
}

/**
 * ╔════════════════════════════════════════════════════════════════════════════╗
 * ║ CONSTANTES DE CATÁLOGO USADAS EN JAVASCRIPT                               ║
 * ║ --------------------------------------------------------------------------║
 * ║ Estas constantes provienen del archivo PHP: config/constantes.php         ║
 * ║ Y se inyectan aquí para que estén disponibles en JS                       ║
 * ║                                                                           ║
 * ║ ⚠️ NO DUPLICAR ESTAS CONSTANTES EN OTROS .js                              ║
 * ╚════════════════════════════════════════════════════════════════════════════╝
 */

// Sirve para inicializar registros en blanco (vacíos)
const CATALOGO_NO_ESPECIFICADO      = {{ config('constantes.CATALOGO_NO_ESPECIFICADO') }};

 // 🧭 Categorías de Catálogo usadas en el sistema, consumos AJAX y llenar select2
const CATEGORIA_ROL_USUARIO             = {{ config('constantes.CATEGORIA_ROL_USUARIO') }};
const CATEGORIA_TIPO_RELACION           = {{ config('constantes.CATEGORIA_TIPO_RELACION') }};
const CATEGORIA_ZONA_SUCURSAL           = {{ config('constantes.CATEGORIA_ZONA_SUCURSAL') }};
const CATEGORIA_TIPO_SUCURSAL           = {{ config('constantes.CATEGORIA_TIPO_SUCURSAL') }};
const CATEGORIA_TIPO_EMPRESA            = {{ config('constantes.CATEGORIA_TIPO_EMPRESA') }};
const CATEGORIA_TIPO_RETIRO             = {{ config('constantes.CATEGORIA_TIPO_RETIRO') }};
const CATEGORIA_TIPO_ESPECIE            = {{ config('constantes.CATEGORIA_TIPO_ESPECIE') }};
const CATEGORIA_TIPO_MATERIA_PRIMA      = {{ config('constantes.CATEGORIA_TIPO_MATERIA_PRIMA') }};
const CATEGORIA_ESTADOS_RETIRO          = {{ config('constantes.CATEGORIA_ESTADOS_RETIRO') }};

const CATEGORIA_REGION_OPERATIVA			= {{ config('constantes.CATEGORIA_REGION_OPERATIVA') }};
const CATEGORIA_TIPO_TRANSPORTE				= {{ config('constantes.CATEGORIA_TIPO_TRANSPORTE') }};

// Valores usados para bifurcar lógicas
const TIPO_EMPRESA_PRODUCTORA       = {{ config('constantes.TIPO_EMPRESA_PRODUCTORA') }};
const TIPO_EMPRESA_TRANSPORTISTA    = {{ config('constantes.TIPO_EMPRESA_TRANSPORTISTA') }};
const TIPO_SUCURSAL_PLANTA          = {{ config('constantes.TIPO_SUCURSAL_PLANTA') }};

// Códigos de Roles de Usuario
const ROL_SOLICITANTE_PLANTA        = {{ config('constantes.ROL_SOLICITANTE_PLANTA') }};
const ROL_SOLICITANTE_PLANTA_XII    = {{ config('constantes.ROL_SOLICITANTE_PLANTA_XII') }};
const ROL_SOLICITANTE_PRODUCTOR     = {{ config('constantes.ROL_SOLICITANTE_PRODUCTOR') }};
const ROL_COORDINADOR               = {{ config('constantes.ROL_COORDINADOR') }};
const ROL_COORDINADOR_XII           = {{ config('constantes.ROL_COORDINADOR_XII') }};
const ROL_ADMINISTRADOR_IT          = {{ config('constantes.ROL_ADMINISTRADOR_IT') }};

// Valores de Tipo de Retiro
const TIPO_RETIRO_TOLVA             = {{ config('constantes.TIPO_RETIRO_TOLVA') }};
const TIPO_RETIRO_BINS              = {{ config('constantes.TIPO_RETIRO_BINS') }};

// Valores de Tipo de Transporte - Retiros de la Región XII
const TIPO_TRANSPORTE_TIERRA        = {{ config('constantes.TIPO_TRANSPORTE_TIERRA') }};
const TIPO_TRANSPORTE_BARCAZA       = {{ config('constantes.TIPO_TRANSPORTE_BARCAZA') }};
const TIPO_TRANSPORTE_COMBINADO     = {{ config('constantes.TIPO_TRANSPORTE_COMBINADO') }};

// Estados de Solicitudes de Retiro
const ESTADO_RETIRO_ESPERANDO       = {{ config('constantes.ESTADO_RETIRO_ESPERANDO') }};
const ESTADO_RETIRO_COMENTADO       = {{ config('constantes.ESTADO_RETIRO_COMENTADO') }};
const ESTADO_RETIRO_ACEPTADO        = {{ config('constantes.ESTADO_RETIRO_ACEPTADO') }};
const ESTADO_RETIRO_PLANIFICADO     = {{ config('constantes.ESTADO_RETIRO_PLANIFICADO') }};
const ESTADO_RETIRO_PROGRAMADO      = {{ config('constantes.ESTADO_RETIRO_PROGRAMADO') }};
const ESTADO_RETIRO_TERMINADO       = {{ config('constantes.ESTADO_RETIRO_TERMINADO') }};
const ESTADO_RETIRO_CANCELADO       = {{ config('constantes.ESTADO_RETIRO_CANCELADO') }};

// Regiones
const REGION_NI                     = {{ config('constantes.REGION_NI') }};
const REGION_I                      = {{ config('constantes.REGION_I') }};
const REGION_II                     = {{ config('constantes.REGION_II') }};
const REGION_III                    = {{ config('constantes.REGION_III') }};
const REGION_IV                     = {{ config('constantes.REGION_IV') }};
const REGION_V                      = {{ config('constantes.REGION_V') }};
const REGION_VI                     = {{ config('constantes.REGION_VI') }};
const REGION_VII                    = {{ config('constantes.REGION_VII') }};
const REGION_VIII                   = {{ config('constantes.REGION_VIII') }};
const REGION_IX                     = {{ config('constantes.REGION_IX') }};
const REGION_X                      = {{ config('constantes.REGION_X') }};
const REGION_XI                     = {{ config('constantes.REGION_XI') }};
const REGION_XII                    = {{ config('constantes.REGION_XII') }};
const REGION_RM                     = {{ config('constantes.REGION_RM') }};
const REGION_XIV                    = {{ config('constantes.REGION_XIV') }};
const REGION_XV                     = {{ config('constantes.REGION_XV') }};

// Regiones operativas que el usuario puede gestionar
//
// IMPORTANTE:
// 		Cuidar que esté sincronizado con 'public function getRegionesOperativasIdsAttribute()'
//		definida en [app\Models\User.php]
//
const REGION_OPERATIVA_POR_ROL      = {	{{ config('constantes.ROL_SOLICITANTE_PRODUCTOR') }}     : 'X/XII',
										{{ config('constantes.ROL_COORDINADOR') }}               : 'X/XII',
										{{ config('constantes.ROL_ADMINISTRADOR_IT') }}          : 'X/XII',

										{{ config('constantes.ROL_PERSONAL_GERENCIA') }}         : 'X/XII',
										{{ config('constantes.ROL_PERSONAL_PRODUCCION') }}       : 'X/XII',
										{{ config('constantes.ROL_PERSONAL_CALIDAD') }}          : 'X/XII',
										{{ config('constantes.ROL_PERSONAL_MANTENCION') }}       : 'X/XII',
										{{ config('constantes.ROL_PERSONAL_ROMANA') }}           : 'X/XII',

										{{ config('constantes.ROL_SOLICITANTE_PLANTA') }}        : 'X',

										{{ config('constantes.ROL_SOLICITANTE_PLANTA_XII') }}    : 'XII',
										{{ config('constantes.ROL_COORDINADOR_XII') }}           : 'XII',
									};

// Tipo de operación requerido en las vistas de Retiros
const TIPO_OPERACION_RETIRO         = {{ config('constantes.TIPO_OPERACION_RETIRO') }};
const TIPO_OPERACION_REPOSICION     = {{ config('constantes.TIPO_OPERACION_REPOSICION') }};

</script>
