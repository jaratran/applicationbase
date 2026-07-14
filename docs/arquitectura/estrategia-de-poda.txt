*** Estrategia de Poda

1) Objetivo:

	ApplicationBase se construye a partir de un proyecto existente mediante una estrategia de poda progresiva.
	El propósito no es eliminar código indiscriminadamente, sino identificar y preservar todas las capacidades reutilizables,
	eliminando únicamente aquellas pertenecientes al dominio específico de la aplicación original.

2) Principios:
	- Ningún componente se elimina sin comprender previamente sus dependencias.
	- Toda eliminación debe estar respaldada por evidencia obtenida mediante inspección del código.
	- En caso de duda, el componente permanece hasta completar su análisis.
	- La prioridad es conservar capacidades reutilizables antes que reducir tamaño del proyecto.
	- La poda se realiza por módulos funcionales completos y no por archivos aislados, salvo en la etapa final de limpieza.
	- Toda decisión arquitectónica estable deberá quedar documentada bajo docs/.

3) Metodología

	- Cada módulo identificado seguirá el siguiente ciclo:

		Inventario.
		Clasificación.
		Análisis de dependencias.
		Decisión.
		Documentación.
		Implementación.
		Validación.
		Clasificación de componentes

	- Cada componente del sistema deberá clasificarse en una de las siguientes categorías:

		Infraestructura Laravel.
		Framework de aplicación.
		Componentes reutilizables.
		Lógica de negocio.
		Código muerto.
		Pendiente de clasificación.
		Orden de trabajo

	- La secuencia de poda estará determinada por el mapa de dependencias entre módulos y no por el tamaño o complejidad aparente de cada uno.
	- Los módulos con menor cantidad de dependencias serán intervenidos primero.
	- Los componentes transversales se analizarán únicamente cuando el resto del sistema ya se encuentre suficientemente desacoplado.
