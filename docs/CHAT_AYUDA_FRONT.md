# Asistente (búho): cómo lo consume el frontend

El asistente es un **chat por diccionario**: el backend compara el texto del usuario con **títulos y palabras clave** de la base de datos y devuelve el **contenido** de la **primera** entrada que coincida (orden `orden`). No hace falta ningún “prompt” en el front.

### Dónde se controla cada cosa (back vs front)

| Problema | Dónde se resuelve |
|----------|-------------------|
| Que con `?modulo=empleados` **no** salgan temas de `general` (“Qué es Talent Sphere”, listado de módulos, etc.) en `data` / `temas_agrupados` / `sugerencias_rapidas` | **Backend** (`GET /chat/ayuda`): la API ya **no mezcla** `general` en esos arrays cuando el módulo activo **no** es `general`. El motor del `POST` mensajes sigue pudiendo usar también entradas `general` internamente para coincidencias. |
| Que el chip no diga “Abrir esta guía” y la etiqueta sea útil | **Backend**: la primera `preguntas[].etiqueta` es el **título del tema**; `enviar` sigue siendo la palabra clave para el POST. |
| Que el chat **no desaparezca** tapado por un panel enorme de chips | **Frontend**: el grid de `temas_agrupados` **no** debe ser un overlay a pantalla completa. Área de chips con **altura máxima** (p. ej. `max-height: min(40vh, 360px)`) + **scroll**; el **historial** (`GET/POST` mensajes) en una columna con **flex-grow** y scroll propio. |
| Burbujas estilo WhatsApp y chips **debajo del último mensaje del usuario** | **Frontend** (obligatorio): seguir `data.presentacion_chat` del `POST` mensajes (ver §4.1). |

---

## Prompt corto para el equipo de frontend (pegar en la tarea / MR)

Implementar el panel del búho así:

1. **Historial primero:** lista de mensajes usuario/asistente con burbujas (usuario a un lado, bot al otro), **siempre visible** con scroll; nunca sustituir el historial por el panel de ayuda.
2. **`temas_agrupados`:** bloque **compacto debajo del header** del módulo o **encima del input**, con `max-height` + `overflow-y: auto`; **prohibido** modal a pantalla completa solo de chips.
3. **Chips:** renderizar solo `temas_agrupados[].preguntas[]`; al pulsar, `POST .../mensajes` con `contenido = enviar` y **`modulo_ayuda`** igual al módulo de la pantalla (`empleados`, etc.).
4. **Tras cada POST:** añadir burbujas nuevas y colocar `sugerencias_relacionadas` **debajo de la última burbuja del usuario** (alineación inicio / columna asistente), según `presentacion_chat`.
5. **No** reimplementar filtros de módulo en cliente para el GET ayuda: confiar en que `data` y `temas_agrupados` ya vienen **solo del módulo** elegido (excepto vista `general` o sin `?modulo`).

---

## 0. Referencia rápida para el equipo de frontend

### Base y autenticación

- **Prefijo API:** `https://<HOST>/api/v1/` (en local suele ser `http://localhost:8000/api/v1/`).
- **Cabecera obligatoria** en todas las rutas del chat: `Authorization: Bearer <JWT>` (mismo token que el resto de la app tras `POST /api/v1/login`).
- **401:** token ausente, inválido o expirado → redirigir a login.
- **422:** cuerpo inválido (p. ej. mensaje vacío) → Laravel devuelve `message` y `errors` por campo.

### Endpoints del asistente

| Método | Ruta | Body | Respuesta (resumen) |
|--------|------|------|---------------------|
| **GET** | `/api/v1/chat/ayuda` | — | Diccionario + `catalogo_modulos` (sin `?modulo`) o vista de módulo (con `?modulo=`). Ver §1. |
| **GET** | `/api/v1/chat/ayuda?modulo=<clave>` | — | Igual; `clave` = `prestaciones_sociales`, `incapacidades`, `general`, etc. |
| **GET** | `/api/v1/chat/conversaciones` | — | Lista de hilos del usuario. |
| **POST** | `/api/v1/chat/conversaciones` | JSON opcional: `{ "titulo": "..." }` | Crea conversación; devuelve `data` con `cod_chat_conversacion`. |
| **DELETE** | `/api/v1/chat/conversaciones/{cod_chat_conversacion}` | — | Borra el hilo (solo si es del usuario). |
| **GET** | `/api/v1/chat/conversaciones/{cod_chat_conversacion}/mensajes` | — | Historial de mensajes (`rol`: usuario / asistente). |
| **POST** | `/api/v1/chat/conversaciones/{cod_chat_conversacion}/mensajes` | **`{ "contenido": "texto", "modulo_ayuda": "empleados" }`** — `contenido` obligatorio (1–8000); `modulo_ayuda` opcional, misma clave que `GET .../ayuda?modulo=` | Guarda mensaje del usuario y respuesta del asistente en `data` (incluye `sugerencias_relacionadas` y `presentacion_chat`). |

### Qué debe implementar el front (checklist)

1. **Al abrir el panel del búho:** `GET /api/v1/chat/ayuda` **sin** query → leer **`catalogo_modulos`** y pintar botones por `etiqueta`; al pulsar, `GET /api/v1/chat/ayuda?modulo=<item.clave>`.
2. **Si el usuario entra desde una pantalla concreta** (ej. prestaciones): puedes ir directo a `GET .../chat/ayuda?modulo=prestaciones_sociales` y saltarte el catálogo.
3. **Con `?modulo=` (y módulo ≠ `general`):** `data`, `sugerencias_rapidas` y `temas_agrupados` traen **solo** filas de ese módulo. Mostrar **`modulo_contexto.etiqueta`**, **`acciones_navegacion`**, y chips desde **`temas_agrupados`** en un área **acotada y con scroll** (no cubrir el historial). Cada chip: `POST .../mensajes` con `contenido: preguntas[].enviar` y `modulo_ayuda` coherente.
4. **“Ver todos los módulos”:** acción con `parametros.modulo === null` → nuevo `GET .../chat/ayuda` sin query.
5. **No cachear** la respuesta de ayuda indefinidamente tras deploys; invalidar al iniciar sesión o al abrir el chat.
6. **Tras enviar mensaje (POST mensajes):** leer `data.contexto`, `data.sugerencias_relacionadas` y `data.presentacion_chat` para continuidad:
   - `contexto.modulo_actual`, `tema_principal`, `cod_entrada_ayuda_match`
   - `sugerencias_relacionadas[]` (hasta **8** por defecto; configurable en backend) con `{ etiqueta, enviar, cod_entrada_ayuda, modulo, prioridad }`. El servidor las arma **en el mismo módulo** que la guía actual o el hilo (última guía visitada en ese módulo), ordenando primero temas “siguientes” en el catálogo y **evitando** repetir lo que el usuario acaba de escribir o temas recientes del hilo.
   - `presentacion_chat`: metadatos de UX (ver §4.1).
   - Al pulsar una sugerencia, enviar de nuevo `POST .../mensajes` con `contenido: sugerencias_relacionadas[].enviar` y el mismo **`modulo_ayuda`** que estés usando en pantalla para no perder contexto.

### Ejemplo de `GET /api/v1/chat/ayuda` (sin query) — forma de la respuesta

```json
{
  "message": "Diccionario del asistente",
  "data": [ { "cod_entrada_ayuda": 1, "titulo": "...", "modulo": "general", "palabras_sugeridas": ["..."], "contenido": "..." } ],
  "sugerencias_rapidas": [ { "cod_entrada_ayuda": 1, "modulo": "general", "etiqueta": "...", "enviar": "..." } ],
  "catalogo_modulos": [
    {
      "clave": "prestaciones_sociales",
      "etiqueta": "Prestaciones sociales",
      "descripcion": "Cesantías, prima, vacaciones…",
      "cantidad_temas": 8,
      "orden": 40,
      "accion": "filtrar_ayuda",
      "parametros": { "modulo": "prestaciones_sociales" }
    }
  ],
  "modulo_contexto": null,
  "acciones_navegacion": [],
  "temas_agrupados": []
}
```

### Ejemplo de `GET /api/v1/chat/ayuda?modulo=prestaciones_sociales`

```json
{
  "message": "Diccionario del asistente",
  "data": [ ],
  "sugerencias_rapidas": [ ],
  "catalogo_modulos": [],
  "modulo_contexto": {
    "clave": "prestaciones_sociales",
    "etiqueta": "Prestaciones sociales",
    "descripcion": "Cesantías, prima, vacaciones, intereses y periodos."
  },
  "acciones_navegacion": [
    { "id": "volver_modulos", "etiqueta": "Ver todos los módulos", "tipo": "filtrar_ayuda", "parametros": { "modulo": null } },
    { "id": "escribir_consulta", "etiqueta": "Escribir mi consulta con mis palabras", "tipo": "foco_input", "parametros": {} }
  ],
  "temas_agrupados": [
    {
      "titulo": "Cómo se calculan las cesantías",
      "modulo": "prestaciones_sociales",
      "cod_entrada_ayuda": 12,
      "preguntas": [
        { "etiqueta": "Cómo se calculan las cesantías", "enviar": "como se calculan las cesantias" },
        { "etiqueta": "¿Base cesantías?", "enviar": "base cesantias" }
      ]
    }
  ]
}
```

*(Con `?modulo=prestaciones_sociales` u otro módulo operativo, `data` / `sugerencias_rapidas` / `temas_agrupados` listan **solo** ese módulo. La vista `?modulo=general` lista solo `general`; sin query, `data` trae todo el diccionario.)*

---

## 1. Cargar el diccionario y las sugerencias

**`GET /api/v1/chat/ayuda`** (JWT igual que el resto de la API).

Respuesta JSON:

| Campo | Uso |
|--------|-----|
| **`data`** | Array de temas. Con **`?modulo=`** y clave **distinta de `general`**, solo entradas de **ese módulo** (sin mezclar `general` para no contaminar la UI). Sin query o con `general`, el criterio es el habitual. |
| **`sugerencias_rapidas`** | Un chip por tema (etiqueta = título, `enviar` = primera palabra clave). Lista plana útil para vista compacta. |
| **`catalogo_modulos`** | Solo cuando **no** envías `?modulo=`: menú de **áreas del sistema** con nombre legible, descripción corta, cantidad de temas y cómo filtrar. |
| **`modulo_contexto`** | Solo con `?modulo=`: `{ clave, etiqueta, descripcion }` del módulo activo (para el encabezado “Estás en: …”). |
| **`acciones_navegacion`** | Solo con `?modulo=`: botones lógicos (**Ver todos los módulos**, **Escribir mi consulta**). Ver §1.1. |
| **`temas_agrupados`** | Solo con `?modulo=`: cada tema con sublista **`preguntas`** (`etiqueta` legible + `enviar` para el POST). Evita mostrar solo palabras sueltas como chips sueltos. |

**Query opcional:** **`?modulo=clave`** (solo letras minúsculas, números y guión bajo, máx. 50 caracteres). Ejemplos: `prestaciones_sociales`, `incapacidades`, `contratos`. Para **`?modulo=empleados`** (cualquier módulo ≠ `general`), el **GET** devuelve en `data`, `sugerencias_rapidas` y `temas_agrupados` **únicamente** ese módulo. El **motor de respuestas** en `POST .../mensajes` sigue pudiendo considerar también entradas `general` cuando corresponde. Con **`?modulo=general`**, solo filas `general`. Si el valor no es válido, el servidor lo **ignora** y devuelve **todo** el diccionario.

### 1.1 Flujo recomendado (menú módulos → temas → volver)

1. **Vista inicial:** `GET .../chat/ayuda` **sin** query. Muestra **`catalogo_modulos`**: cada ítem trae `accion: "filtrar_ayuda"` y `parametros: { "modulo": "<clave>" }`. Al pulsar, el front **vuelve a llamar** `GET .../chat/ayuda?modulo=<clave>` (no hace falta POST para cambiar de menú).
2. **Vista de módulo:** Con `?modulo=`, usa **`modulo_contexto`** para el título, **`acciones_navegacion`** para:
   - **`tipo` = `filtrar_ayuda`** y `parametros.modulo` = `null` → nueva petición **sin** `?modulo=` (volver al catálogo de áreas).
   - **`tipo` = `foco_input`** → enfocar el textarea (el usuario escribe; el motor sigue buscando en **todo** el diccionario al enviar el mensaje).
3. **Preguntas del módulo:** Renderiza **`temas_agrupados`** en zona **no invasiva** (altura limitada + scroll). Por cada `titulo`, muestra chips de `preguntas[].etiqueta`: la **primera** usa el **título del tema** como etiqueta y `enviar` es la primera palabra clave; las siguientes son frases derivadas de otras claves (máx. reducido en servidor). Al pulsar, **`POST .../mensajes`** con `contenido = enviar` y **`modulo_ayuda`** del módulo abierto.
4. **Opcional:** Puedes ocultar **`palabras_sugeridas`** en UI y basarte solo en **`temas_agrupados`** + **`catalogo_modulos`** para no duplicar chips confusos.

Las etiquetas de módulos y el orden del menú vienen de **`config/chat_modulos.php`**; los módulos sin definición usan nombre derivado de la clave. Solo entran al catálogo módulos que tengan al menos una fila activa en `chat_entradas_ayuda`.

Objeto en **`sugerencias_rapidas`**:

- **`etiqueta`**: texto a mostrar en el chip (título del tema, acortado).
- **`enviar`**: texto que debe ir en el cuerpo del mensaje para disparar ese tema (primera palabra clave del tema; si no hay claves, un resumen del título).
- **`modulo`**, **`cod_entrada_ayuda`**: para depuración o analytics.

## 2. Enviar el mensaje (igual escribiendo o pulsando un chip)

1. Crear o reutilizar conversación: **`POST /api/v1/chat/conversaciones`**.
2. Enviar mensaje: **`POST /api/v1/chat/conversaciones/{id}/mensajes`** con JSON `{ "contenido": "<texto>", "modulo_ayuda": "empleados" }` (el segundo campo es **opcional** pero muy recomendable si el usuario eligió un módulo o entró desde una pantalla concreta).

Cuando el usuario **pulsa un chip**, debe enviarse exactamente **`contenido` = `enviar`** de esa sugerencia (o cualquier otro texto que contenga una palabra clave del tema). El servidor no distingue si lo escribió a mano o vino de un chip.

### 2.1 Respuesta del POST (campos útiles)

| Campo | Uso |
|--------|-----|
| `mensaje_usuario` | Fila guardada del usuario (`rol`, `contenido`, `created_at`). |
| `mensaje_asistente` | Respuesta del asistente. |
| `contexto` | `modulo_actual`, `tema_principal`, `cod_entrada_ayuda_match` (match del diccionario o contexto del hilo). |
| `sugerencias_relacionadas` | Chips del **siguiente paso** coherentes con el módulo y el hilo. |
| `presentacion_chat` | Indicaciones de layout; ver §4.1. |

## 3. Mapear la ruta de la app → `modulo` y query

Al abrir el panel del búho, llamar por ejemplo:

- Pantalla prestaciones → `GET .../chat/ayuda?modulo=prestaciones_sociales`
- Pantalla incapacidades → `...?modulo=incapacidades`
- Sin pantalla concreta (menú general) → `GET .../chat/ayuda` sin query.

Mapeo sugerido (misma clave que en el seed / BD):

| Ruta o sección aproximada | `modulo` |
|---------------------------|----------|
| Temas transversales (si solo quieres esos en la lista) | `general` |
| Empleados | `empleados` |
| Contratos | `contratos` |
| Cargos / bancos | `cargos` / `bancos` |
| Incapacidades | `incapacidades` |
| Tipos incapacidad / CIE | `catalogos_incapacidad` |
| Afiliaciones | `afiliaciones` |
| EPS, ARL, pensiones, etc. | `catalogos_afiliacion` |
| Prestaciones | `prestaciones_sociales` |
| Certificaciones | `certificaciones` |
| Inasistencias | `inasistencias` |
| Calendario | `calendario` |
| Reportes | `reportes` |
| Disciplinarias | `disciplinarias` |
| Empresas | `empresas` |
| Login / perfil | `autenticacion` |
| Usuarios admin | `administracion_usuarios` |
| Ayuda del propio chat | `asistente_chat` |

## 4. Renderizado

- **`contenido`** en `data[]` puede incluir Markdown ligero (`**negrita**`); conviene renderizarlo o mostrarlo plano según el diseño.
- Tras un deploy que cambie textos de ayuda: **volver a llamar** a `GET /chat/ayuda` (evitar caché eterna).

### 4.1 Registro tipo mensajería (WhatsApp) y posición de sugerencias

El backend devuelve en cada POST **`presentacion_chat`** para alinear el front con una UX clara:

```json
"presentacion_chat": {
  "registro_estilo": "mensajeria",
  "sugerencias_relacionadas": {
    "ubicacion": "debajo_ultimo_mensaje_usuario",
    "alineacion": "inicio",
    "columna": "asistente_izquierda",
    "nota": "…"
  }
}
```

**Recomendación de interfaz**

1. **Lista del hilo** (`GET .../mensajes` o mensajes acumulados en memoria): mostrar cada turno como **burbujas** — usuario a la derecha (o el estilo que ya use la app) y asistente a la **izquierda**, con fecha/hora opcional entre bloques, similar a WhatsApp u otras apps de chat.
2. **Tras cada `POST .../mensajes`**: añadir primero la burbuja del **`mensaje_usuario`**, luego la del **`mensaje_asistente`**, en orden cronológico (`created_at`).
3. **`sugerencias_relacionadas`**: pintarlas **solo debajo de la burbuja del último mensaje del usuario de ese turno** (no debajo de la respuesta del bot), con **alineación al inicio** (`text-align: start` / `align-items: flex-start` en LTR = **izquierda**), en la **misma columna visual** que las respuestas del asistente. Así se entiende que son “siguientes pasos” sugeridos para el funcionario, no texto del bot.
4. Al enviar otra pregunta, **sustituir** o desplazar hacia arriba el bloque de chips anterior para no mezclar sugerencias viejas con el nuevo turno.

Con esto el hilo queda legible y las sugerencias se sienten **ancladas al contexto** del mensaje que el usuario acaba de enviar.

## 5. Backend / despliegue

- Migración columna `modulo`: `2026_04_09_100000_add_modulo_to_chat_entradas_ayuda.php`
- Datos: `database/data/chat_entradas_ayuda_seed.php` + `php artisan db:seed --class=ChatEntradaAyudaSeeder`
