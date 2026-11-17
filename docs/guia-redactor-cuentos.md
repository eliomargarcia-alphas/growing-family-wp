# Guía para subir un cuento (Growing Family)

Esta guía es para el redactor de contenido que publica cuentos en WordPress.
Sigue los pasos en orden y revisa la lista final antes de publicar.

---

## 1. Antes de empezar

Necesitas:

- Acceso al panel de WordPress con permiso de **editor** (o superior).
- El texto del cuento, dividido en páginas (ver sección 4).
- Una **portada** (imagen destacada), con proporción **4:3** y al menos **1200 px** de ancho.
- Si el cuento tiene dos protagonistas posibles (Ana y Max), la **ilustración alternativa** de la otra portada (opcional, ver sección 3).
- Las **fuentes** del contenido (título, organismo y enlace) si el cuento trata un tema de salud emocional.

---

## 2. Crear el cuento

1. En el panel, ve a **Cuentos → Añadir nuevo**.
2. Escribe el **título** del cuento. Se verá en el listado y en la página del cuento.
3. Escribe el **extracto** (resumen de 1 o 2 frases). Si lo dejas vacío, el sistema toma las primeras líneas del campo "Así empieza".
4. Asigna la **imagen destacada** (la portada).

---

## 3. Campos del cuento

Los campos están en el bloque de datos del cuento. Algunos son obligatorios.

### Página del cuento

| Campo | Obligatorio | Qué poner |
|---|---|---|
| **Protagonista por defecto** | Sí | `Ana` o `Max`. Repártelos entre los cuentos; no los elijas al azar. Es la versión que leen Google y los asistentes de IA. |
| **Ilustración alternativa** | No | Portada del otro protagonista. Si la dejas vacía, la portada no cambia al cambiar de protagonista. |
| **Así empieza (resumen)** | Sí | Dos frases sobre de qué va el cuento, sin spoilers. Se muestra en la tarjeta del listado. |
| **Minutos para contarlo** | No | Vacío: se calcula solo (unas 90 palabras por minuto). Escribe un número solo si quieres forzarlo. |

### Cómo contarlo

| Campo | Obligatorio | Qué poner |
|---|---|---|
| **Cómo contarlo** | No | Texto de la pantalla "Antes de empezar". Vacío: se usa el texto por defecto de **Cuentos → Ajustes**. |

### Qué trabaja

| Campo | Obligatorio | Qué poner |
|---|---|---|
| **Qué trabaja y por qué** | Sí | Explicación breve del valor del cuento para el niño y la familia. |
| **Fuentes** | Sí | Una por línea, con el formato: `Título \| Organismo \| URL`. |

### Enlaces y revisión

| Campo | Obligatorio | Qué poner |
|---|---|---|
| **Guía relacionada** | No | Guía de la web que complementa el cuento. Si falta, el administrador lo verá en el listado. |
| **Pilar** | No | Pilar (desarrollo) relacionado. |
| **Requiere firma experta** | No | Márcalo solo en temas delicados (una pérdida, miedos intensos…). Al marcarlo aparecen **Revisado por** y **Fecha de revisión**: rellénalos cuando la persona experta haya revisado el texto. |

### Términos (columna lateral)

Asigna al menos **una emoción** y **una edad**. Son los filtros del listado.

- **Emoción:** Miedo, Rabia, Tristeza, Celos, Frustración, Alegría.
- **Situación:** Llega un hermanito, Primer día de colegio, Una pérdida, Una mudanza, Miedo a la oscuridad.
- **Edad:** 4 años, 5 años, 6 años.
- **Para contar:** Para dormir; Corto, para contar en 5 minutos.

> Si necesitas una emoción o situación nueva, pídela al equipo técnico: hay que añadir también su emoji a la tarjeta.

### Destacado

Marca **Destacado** solo para los cuentos que quieras que aparezcan primero en su listado.

---

## 4. Escribir las páginas

El cuento se divide en **páginas** con el bloque **Salto de página**. Lo que hay entre dos saltos es una página.

Cada página puede llevar:

- **Texto** en párrafos normales.
- **Ilustración** con el bloque **Imagen** (o **Galería**). Se muestra a la izquierda en escritorio.

### Estilos especiales (en el panel de bloques)

Selecciona un bloque de párrafo o imagen y elige el estilo en la barra lateral:

| Estilo | Úsalo para | Qué hace |
|---|---|---|
| **Nota: pregunta** | Una pregunta para hacer al niño | Muestra la etiqueta "Pregunta para tu hija o hijo". |
| **Tono: alegre / sorpresa / susurro / grito / triste** | Indicar cómo leer ese párrafo | Muestra un icono y el tono. |
| **Solo Ana** / **Solo Max** | Una ilustración que solo debe verse con un protagonista | Se oculta con el otro protagonista. Sin estilo, vale para los dos. |

### Variantes Ana / Max

Para que el texto cambie según el protagonista, escribe las dos versiones entre llaves y barra:

- `{Ana|Max} encontró su osito.`
- `Ana estaba {asustada|asustado}.`

Se muestra la versión del protagonista elegido, y el lector puede cambiarla con el selector de la página.

### Reglas de estilo

- Frases cortas y lenguaje cálido, pensado para leerse en voz alta.
- Una idea por página.
- Sin palabras que asusten de forma gratuita; si el tema es intenso, márcalo con **Requiere firma experta**.

---

## 5. Revisar antes de publicar

Marca cada punto antes de publicar:

- [ ] Título y extracto escritos.
- [ ] Imagen destacada (portada) en 4:3, al menos 1200 px.
- [ ] Protagonista por defecto asignado (Ana o Max, repartido).
- [ ] "Así empieza" con dos frases.
- [ ] "Qué trabaja y por qué" escrito.
- [ ] Fuentes en el formato `Título | Organismo | URL`, con enlaces que funcionan.
- [ ] Al menos una emoción, una edad y, si aplica, una situación.
- [ ] Páginas separadas con **Salto de página**.
- [ ] Las preguntas llevan el estilo **Nota: pregunta**.
- [ ] Si el tema es delicado: **Requiere firma experta** marcado, con **Revisado por** y **Fecha de revisión**.
- [ ] Vista previa revisada en móvil y en escritorio.

Cuando todo esté listo, pulsa **Publicar**.

---

## 6. Problemas frecuentes

| Problema | Causa probable | Qué hacer |
|---|---|---|
| El cuento no aparece en el listado | Falta emoción o edad, o no está publicado | Revisa los términos y que el estado sea "Publicado". |
| La tarjeta muestra 📚 en vez de un emoji | La emoción no tiene emoji en la plantilla | Avisa al equipo técnico. |
| No cambia la portada al elegir el otro protagonista | No hay ilustración alternativa | Sube la portada del otro protagonista en ese campo. |
| Los minutos salen mal | Hay un valor fijo en "Minutos para contarlo" | Vacía el campo para que se calcule solo. |

---

## 7. Notas para el equipo técnico

- La tarjeta de cada cuento muestra siempre "Para leer con: Ana • Max" y el aval "Incluye pregunta de conexión guiada para padres". Ambos textos son fijos en la plantilla `gwf-listado-cuentos.php`, no dependen del cuento.
- Desajuste detectado: el mapa de emojis usa `enfado` y `frustración`, pero los términos sembrados son `rabia` y `frustracion`. Hay que alinear uno con otro.
