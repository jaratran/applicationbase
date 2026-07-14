# Estrategia de Transformación

## Objetivo

	ApplicationBase se construye a partir de un proyecto existente mediante una estrategia de transformación progresiva.
	El objetivo no es reducir el tamaño del proyecto, sino identificar, desacoplar y conservar todas las capacidades reutilizables,
	eliminando únicamente aquellas que pertenecen al dominio específico de la aplicación original.

---

## Principios

	1. Ningún componente se modifica sin comprender previamente sus dependencias.
	2. Toda decisión debe estar respaldada por evidencia obtenida mediante inspección del código.
	3. En caso de duda, el componente permanece hasta completar su análisis.
	4. El objetivo principal es construir un framework de aplicación reutilizable.
	5. La eliminación de código es consecuencia del desacoplamiento y no un objetivo en sí misma.
	6. Toda decisión arquitectónica estable deberá incorporarse a la documentación del proyecto.

---

## Modelo de trabajo

	Cada capacidad seguirá el siguiente ciclo:

	1. Comprensión del estado actual (AS-IS).
	2. Diseño del estado objetivo (TO-BE).
	3. Identificación de dependencias.
	4. Desacoplamiento.
	5. Incorporación al Framework de Aplicación.
	6. Eliminación del código específico del dominio.
	7. Validación.

---

## Clasificación de componentes

	Cada elemento del sistema deberá clasificarse como:

	- Infraestructura Laravel
	- Framework de Aplicación
	- Lógica de Negocio
	- Código Muerto
	- Pendiente de Clasificación

---

## Capacidades

	ApplicationBase se construirá sobre capacidades reutilizables y no sobre módulos de negocio.

	Una capacidad podrá agrupar uno o varios módulos del proyecto original.

	Ejemplos:

	- Identidad
	- Seguridad
	- Usuarios
	- Organización
	- Catálogos
	- Parámetros
	- Configuración
	- Presentación
	- Notificaciones
	- Auditoría
	- Utilidades

---

## Orden de transformación

	El orden de intervención será determinado por el nivel de acoplamiento y las dependencias entre capacidades.
	Siempre se desacoplarán primero las capacidades reutilizables y posteriormente se eliminará la lógica específica del dominio.

---

## Documentación

	Los chats se utilizarán para analizar y discutir alternativas.
	Las decisiones estables deberán incorporarse a la documentación ubicada bajo `docs/`, la cual constituye la fuente oficial de
	conocimiento del proyecto.

---

## Unidad de transformación

Durante la evolución de ApplicationBase, la unidad de trabajo será la **capacidad**.
Una capacidad representa una responsabilidad funcional del framework y puede estar implementada por uno o varios módulos,
componentes o archivos del proyecto original.

Cada iteración de transformación seguirá el siguiente ciclo:

	1. Delimitar la capacidad.
	2. Identificar los componentes que la implementan.
	3. Analizar sus dependencias.
	4. Desacoplarla de la lógica de negocio.
	5. Incorporarla al Framework de Aplicación.
	6. Validar su funcionamiento.
	7. Eliminar el código específico del dominio que haya quedado sin uso.

Este enfoque evita trabajar sobre archivos aislados y permite mantener la coherencia arquitectónica durante todo
el proceso de transformación.

---

## Clasificación previa a la transformación

	Antes de transformar una capacidad, todos sus componentes deberán clasificarse según el ámbito al que pertenecen.

	Se utilizarán tres ámbitos claramente diferenciados:

	- **Laravel Framework**: componentes proporcionados por Laravel que deben mantenerse alineados con el framework y no forman parte de ApplicationBase.
	- **ApplicationBase**: componentes reutilizables que constituyen el framework desarrollado sobre Laravel y que podrán ser utilizados por múltiples aplicaciones.
	- **Dominio de la aplicación**: componentes específicos del problema que resuelve una aplicación concreta y que no deberán incorporarse al framework.

	La transformación de una capacidad comenzará únicamente cuando sus componentes hayan sido clasificados completamente.

	Esta clasificación constituye la línea base para cualquier refactorización posterior y permite justificar objetivamente cada conservación, generalización o eliminación.

---

## Corrección de inconsistencias

	Una vez completada la clasificación de una capacidad, y antes de iniciar su transformación arquitectónica, deberá verificarse su consistencia interna.

	Esta etapa tiene como objetivo eliminar deuda técnica preexistente sin alterar la arquitectura ni las responsabilidades de la capacidad.

	Se consideran inconsistencias, entre otras:

	- Referencias a recursos inexistentes.
	- Claves incorrectas o mal referenciadas.
	- Namespaces inconsistentes.
	- Duplicidades evidentes.
	- Errores de nomenclatura.
	- Referencias obsoletas.

	Durante esta etapa no deberán introducirse nuevas funcionalidades ni reorganizar componentes.

	El objetivo es garantizar que la capacidad se encuentre en un estado consistente antes de comenzar su desacoplamiento y transformación.

---

## Principio de clasificación por ámbito

	Antes de iniciar la transformación de cualquier capacidad, todos sus componentes deberán clasificarse según el ámbito al que pertenecen.

	Se utilizarán siempre las siguientes categorías:

	Laravel Framework

	Corresponde a funcionalidades propias del framework Laravel o a convenciones estándar que no forman parte del diseño de ApplicationBase.

	Ejemplos:

	autenticación estándar;
	validaciones estándar;
	paginación;
	traducciones base del framework;
	middleware propios de Laravel;
	configuración estándar.

	Estos elementos normalmente se conservan tal como los proporciona Laravel, salvo necesidades de actualización o configuración.

	ApplicationBase

	Corresponde a capacidades reutilizables que formarán parte permanente del framework ApplicationBase y que podrán ser utilizadas por cualquier aplicación desarrollada sobre él.

	Ejemplos:

	gestión de usuarios;
	configuración visual;
	parámetros generales;
	catálogos reutilizables;
	notificaciones genéricas;
	componentes de interfaz;
	auditoría;
	gestión documental;
	localización e internacionalización.

	Estos elementos podrán evolucionar, pero deberán mantenerse independientes de cualquier dominio de negocio.

	Dominio de la aplicación

	Corresponde a reglas, procesos, modelos, mensajes, vistas, validaciones o configuraciones propias de una aplicación concreta.

	Ejemplos en el proyecto heredado La Portada:

	solicitudes de retiro;
	planificación;
	programa diario;
	camiones;
	conductores;
	maquilas;
	Telegram para conductores;
	estados operacionales;
	regiones operativas.

	Estos elementos no forman parte de ApplicationBase y deberán aislarse antes de ser eliminados o reemplazados.

	Regla de transformación

	Ninguna capacidad podrá comenzar su transformación mientras sus componentes no hayan sido clasificados completamente en una de las tres categorías anteriores.

	La clasificación constituye el primer paso obligatorio de cualquier proceso de análisis y permite separar con claridad:

	aquello que pertenece a Laravel;
	aquello que debe incorporarse al framework ApplicationBase;
	aquello que corresponde exclusivamente al dominio de la aplicación heredada.

	Este criterio será aplicado de forma uniforme durante toda la construcción de ApplicationBase y se considera una regla permanente del proyecto.

---

## Principio de no intervención sobre componentes destinados a poda

	Durante la construcción de ApplicationBase no se invertirán esfuerzos en corregir, optimizar o refactorizar componentes cuya clasificación definitiva corresponda al dominio específico de la aplicación heredada.

	Una vez que una capacidad haya sido clasificada como Dominio, el objetivo principal será comprender sus dependencias para planificar su eliminación, no mejorar su implementación.

	En consecuencia:

	no se corregirán defectos menores;
	no se completarán traducciones;
	no se normalizarán validaciones;
	no se optimizará código;
	no se realizarán refactorizaciones internas.

	Las únicas excepciones serán:

	errores que impidan inspeccionar el código;
	errores que bloqueen la compilación o ejecución necesaria para continuar el análisis;
	correcciones imprescindibles para desacoplar una capacidad reutilizable.

	Este principio permite concentrar el esfuerzo exclusivamente en las capacidades que formarán parte de ApplicationBase, evitando dedicar tiempo a mejorar código cuya eliminación ya ha sido decidida.

---

## Principio de poda incremental

	La eliminación de componentes del dominio no se realizará según su importancia funcional, sino según su nivel de acoplamiento con el resto del sistema.

	Siempre que sea posible, la secuencia de poda seguirá un enfoque incremental:

	comenzar por capacidades periféricas y con pocas dependencias;
	continuar con módulos cuyo desacoplamiento ya haya sido facilitado por eliminaciones previas;
	dejar para las últimas etapas los componentes que actúan como núcleo del dominio o concentran múltiples relaciones.

	Antes de iniciar la eliminación de cualquier capacidad deberá conocerse:

	sus dependencias salientes;
	sus dependencias entrantes;
	los componentes que dejarán de utilizarla;
	los componentes que todavía la requieren.

	La planificación de la poda tendrá prioridad sobre la ejecución de la poda. Solo cuando exista una hoja de ruta validada se iniciará la eliminación efectiva de código.