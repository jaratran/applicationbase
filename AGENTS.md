# AGENTS.md

## Objetivo

ApplicationBase se construye mediante la poda sistemática de una aplicación Laravel existente.

La prioridad es obtener un núcleo limpio, reutilizable y desacoplado del dominio de negocio.

---

# Metodología

Trabajar siempre sobre una única capacidad.

Evitar análisis globales cuando no aporten valor directo.

Favorecer modificaciones pequeñas y verificables.

Utilizar el código como fuente principal de información.

---

# Clasificación

Toda capacidad debe clasificarse rápidamente como:

- Laravel Framework
- ApplicationBase
- Dominio

Las capacidades del Dominio deben eliminarse.

Las capacidades de ApplicationBase deben conservarse o generalizarse.

Laravel debe permanecer intacto salvo necesidad justificada.

---

# Poda

Durante la poda:

- no refactorizar código destinado a desaparecer;
- corregir únicamente dependencias residuales necesarias;
- utilizar los errores como guía para descubrir acoplamientos;
- evitar soluciones transitorias para conservar componentes del dominio.

Si una decisión requiere cambiar el orden de poda, advertirlo antes de continuar.

---

# Código

Priorizar siempre:

- simplicidad;
- legibilidad;
- coherencia con el proyecto.

No introducir abstracciones innecesarias.

No crear infraestructura para resolver un problema puntual.

---

# Comentarios

Toda modificación debe revisar también los comentarios.

Eliminar:

- comentarios obsoletos.

Conservar:

- comentarios aún vigentes.

Actualizar:

- comentarios cuyo propósito haya cambiado.

Los comentarios deben explicar el propósito del bloque y no repetir literalmente el código.

---

# Documentación

Generar documentación únicamente cuando:

- establezca una decisión arquitectónica permanente;
- defina una convención del proyecto;
- sirva como referencia futura para ApplicationBase.

Evitar documentación descriptiva de código destinado a desaparecer.

---

# Validaciones

Después de cada iteración:

- revisar referencias residuales;
- ejecutar las validaciones disponibles;
- corregir únicamente los errores derivados de la poda realizada.

No continuar automáticamente con la siguiente capacidad.

Esperar siempre la revisión del resultado.

---

# Principio general

Cuando exista una alternativa claramente más simple, preferirla.

Cuando una capacidad vaya a eliminarse posteriormente, evitar invertir esfuerzo en mejorarla.