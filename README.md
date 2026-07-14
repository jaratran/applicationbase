# ApplicationBase

ApplicationBase es un proyecto cuyo objetivo es obtener una base reutilizable para aplicaciones Laravel mediante la poda sistemática de una aplicación existente.

La estrategia consiste en eliminar progresivamente todas las capacidades pertenecientes al dominio de negocio, conservando únicamente aquellas que sean propias de Laravel o reutilizables en cualquier aplicación.

## Objetivos

- Obtener un núcleo limpio y reutilizable.
- Reducir dependencias del dominio.
- Mantener una arquitectura simple.
- Favorecer la evolución incremental.

## Estado

El proyecto se encuentra actualmente en proceso de poda.

Las capacidades del dominio se eliminan iteración por iteración.

Las decisiones permanentes se documentan dentro de `docs/`.

## Tecnologías

- Laravel
- Bootstrap 5
- DataTables
- Chart.js

## Organización

```
app/
resources/
routes/
database/
docs/
```

## Documentación

La documentación arquitectónica se encuentra en `docs/`.

Las instrucciones de trabajo para agentes de IA se encuentran en `AGENTS.md`.