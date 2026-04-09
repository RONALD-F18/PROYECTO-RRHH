# Asistente (búho): cómo lo consume el frontend

El asistente es un **chat por diccionario**: el backend compara el texto del usuario con **títulos y palabras clave** de la base de datos y devuelve el **contenido** de la **primera** entrada que coincida (orden `orden`). No hace falta ningún “prompt” en el front.

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
| **POST** | `/api/v1/chat/conversaciones/{cod_chat_conversacion}/mensajes` | **`{ "contenido": "texto" }`** obligatorio, string 1–8000 chars | Guarda mensaje del usuario y respuesta del asistente en `data`. |

### Qué debe implementar el front (checklist)

1. **Al abrir el panel del búho:** `GET /api/v1/chat/ayuda` **sin** query → leer **`catalogo_modulos`** y pintar botones por `etiqueta`; al pulsar, `GET /api/v1/chat/ayuda?modulo=<item.clave>`.
2. **Si el usuario entra desde una pantalla concreta** (ej. prestaciones): puedes ir directo a `GET .../chat/ayuda?modulo=prestaciones_sociales` y saltarte el catálogo.
3. **Con `?modulo=`:** mostrar **`modulo_contexto.etiqueta`**, botones de **`acciones_navegacion`**, y preguntas con **`temas_agrupados`** (chips `preguntas[].etiqueta` → al pulsar, `POST .../mensajes` con `contenido: preguntas[].enviar`).
4. **“Ver todos los módulos”:** acción con `parametros.modulo === null` → nuevo `GET .../chat/ayuda` sin query.
5. **No cachear** la respuesta de ayuda indefinidamente tras deploys; invalidar al iniciar sesión o al abrir el chat.
6. **Tras enviar mensaje (POST mensajes):** leer `data.contexto` y `data.sugerencias_relacionadas` para continuidad:
   - `contexto.modulo_actual`, `tema_principal`, `cod_entrada_ayuda_match`
   - `sugerencias_relacionadas[]` (3–5) con `{ etiqueta, enviar, cod_entrada_ayuda, modulo, prioridad }`
   - al pulsar una sugerencia relacionada, volver a enviar su `enviar` como `contenido`.

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
        { "etiqueta": "Cómo se calculan las cesantías", "enviar": "cesantias" },
        { "etiqueta": "Relacionado: cesantía", "enviar": "cesantía" }
      ]
    }
  ]
}
```

*(Los arrays `data` / `sugerencias_rapidas` en el ejemplo de módulo vienen acortados; en producción traen todas las filas del módulo + `general`.)*

---

## 1. Cargar el diccionario y las sugerencias

**`GET /api/v1/chat/ayuda`** (JWT igual que el resto de la API).

Respuesta JSON:

| Campo | Uso |
|--------|-----|
| **`data`** | Array de temas. Cada objeto es una fila del diccionario **más** el array **`palabras_sugeridas`**. |
| **`sugerencias_rapidas`** | Un chip por tema (etiqueta = título, `enviar` = primera palabra clave). Lista plana útil para vista compacta. |
| **`catalogo_modulos`** | Solo cuando **no** envías `?modulo=`: menú de **áreas del sistema** con nombre legible, descripción corta, cantidad de temas y cómo filtrar. |
| **`modulo_contexto`** | Solo con `?modulo=`: `{ clave, etiqueta, descripcion }` del módulo activo (para el encabezado “Estás en: …”). |
| **`acciones_navegacion`** | Solo con `?modulo=`: botones lógicos (**Ver todos los módulos**, **Escribir mi consulta**). Ver §1.1. |
| **`temas_agrupados`** | Solo con `?modulo=`: cada tema con sublista **`preguntas`** (`etiqueta` legible + `enviar` para el POST). Evita mostrar solo palabras sueltas como chips sueltos. |

**Query opcional:** **`?modulo=clave`** (solo letras minúsculas, números y guión bajo, máx. 50 caracteres). Ejemplos: `prestaciones_sociales`, `incapacidades`, `contratos`. El servidor devuelve las entradas de **ese módulo** **y además** todas las filas con **`modulo` = `general`**. Si usas **`?modulo=general`**, solo verás las de `general`. Si el valor no es válido, el servidor lo **ignora** y devuelve **todo** el diccionario.

### 1.1 Flujo recomendado (menú módulos → temas → volver)

1. **Vista inicial:** `GET .../chat/ayuda` **sin** query. Muestra **`catalogo_modulos`**: cada ítem trae `accion: "filtrar_ayuda"` y `parametros: { "modulo": "<clave>" }`. Al pulsar, el front **vuelve a llamar** `GET .../chat/ayuda?modulo=<clave>` (no hace falta POST para cambiar de menú).
2. **Vista de módulo:** Con `?modulo=`, usa **`modulo_contexto`** para el título, **`acciones_navegacion`** para:
   - **`tipo` = `filtrar_ayuda`** y `parametros.modulo` = `null` → nueva petición **sin** `?modulo=` (volver al catálogo de áreas).
   - **`tipo` = `foco_input`** → enfocar el textarea (el usuario escribe; el motor sigue buscando en **todo** el diccionario al enviar el mensaje).
3. **Preguntas del módulo:** Renderiza **`temas_agrupados`**: por cada `titulo`, muestra chips de `preguntas[].etiqueta` y al pulsar envía **`preguntas[].enviar`** en `POST .../mensajes` como `contenido`. La primera pregunta de cada tema repite el título como etiqueta; el resto lleva prefijo “Relacionado: …”.
4. **Opcional:** Puedes ocultar **`palabras_sugeridas`** en UI y basarte solo en **`temas_agrupados`** + **`catalogo_modulos`** para no duplicar chips confusos.

Las etiquetas de módulos y el orden del menú vienen de **`config/chat_modulos.php`**; los módulos sin definición usan nombre derivado de la clave. Solo entran al catálogo módulos que tengan al menos una fila activa en `chat_entradas_ayuda`.

Objeto en **`sugerencias_rapidas`**:

- **`etiqueta`**: texto a mostrar en el chip (título del tema, acortado).
- **`enviar`**: texto que debe ir en el cuerpo del mensaje para disparar ese tema (primera palabra clave del tema; si no hay claves, un resumen del título).
- **`modulo`**, **`cod_entrada_ayuda`**: para depuración o analytics.

## 2. Enviar el mensaje (igual escribiendo o pulsando un chip)

1. Crear o reutilizar conversación: **`POST /api/v1/chat/conversaciones`**.
2. Enviar mensaje: **`POST /api/v1/chat/conversaciones/{id}/mensajes`** con JSON `{ "contenido": "<texto>" }`.

Cuando el usuario **pulsa un chip**, debe enviarse exactamente **`contenido` = `enviar`** de esa sugerencia (o cualquier otro texto que contenga una palabra clave del tema). El servidor no distingue si lo escribió a mano o vino de un chip.

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

## 5. Backend / despliegue

- Migración columna `modulo`: `2026_04_09_100000_add_modulo_to_chat_entradas_ayuda.php`
- Datos: `database/data/chat_entradas_ayuda_seed.php` + `php artisan db:seed --class=ChatEntradaAyudaSeeder`
