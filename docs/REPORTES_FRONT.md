# Reportes RRHH — guía para frontend

Documento técnico alineado con `ReporteController`, `ReporteRequest` y `ReporteService`.

---

## Prompt (copiar y pegar para el equipo / IA del front)

```
Implementa la pantalla de reportes contra la API de Talent Sphere.

- Endpoint: POST /api/v1/reportes/generar
- Autenticación: header Authorization: Bearer <JWT> (mismo token que el resto de la app).
- Cuerpo JSON obligatorio:
  - modulo: uno de empleados | contratos | prestaciones | incapacidades | inasistencias | afiliaciones | disciplinario
  - tipo: string requerido, máx. 50 caracteres (hoy el backend genera siempre el “resumen general” por módulo; puedes enviar por ejemplo "resumen_general" en todos los casos hasta que existan más tipos).
  - params (opcional objeto):
    - cod_empleado: número, opcional, debe existir en empleados
    - cod_contrato: número, opcional, debe existir en contrato
    - tipo_certificacion: string opcional, máx. 30
    - descripcion: string opcional, máx. 150 (se guarda en historial del reporte)

- Respuesta: NO es JSON. Es un PDF en streaming (application/pdf). Usa responseType 'blob' o arraybuffer, crea URL con URL.createObjectURL y abre descarga o vista en nueva pestaña. El nombre sugerido viene en Content-Disposition (reporte-<modulo>-<timestamp>.pdf).

- Errores: 401 sin token; 422 con JSON { message, errors } si modulo/tipo/params inválidos.

- UI sugerida: selector de módulo → botón “Generar PDF”; campos opcionales de filtro (empleado/contrato/descripción) si la pantalla lo requiere.

Historial (React, sin localStorage):
- GET  /api/v1/reportes/registros?modulo=&fecha_desde=&fecha_hasta=  → JSON { data: [ ... ] } orden descendente por created_at. Funcionario: solo sus filas. Administrador: todas.
- POST /api/v1/reportes/registros  → body { modulo, tipo, estado, descripcion? }; 201 { data: { id, modulo, tipo, estado, descripcion, created_at, nombre_usuario, usuario? } }.
- DELETE /api/v1/reportes/registros/{id} → 204 sin cuerpo; 403 si no es dueño ni administrador.
Tras generar el PDF con éxito, el front debe registrar el evento con POST (no duplicar con otra inserción en el backend salvo que acorden un solo origen).
```

---

## Historial en base de datos (`reporte_registros`)

| Método | Ruta | Descripción |
|--------|------|-------------|
| **GET** | `{BASE}/api/v1/reportes/registros` | Listado JSON `{ "data": [ ... ] }` |
| **POST** | `{BASE}/api/v1/reportes/registros` | Alta de fila de historial |
| **DELETE** | `{BASE}/api/v1/reportes/registros/{id}` | Borrado (dueño o administrador) |

**Query GET (opcionales):** `modulo` (mismo enum que PDF), `fecha_desde`, `fecha_hasta` (filtran por fecha de `created_at`, día calendario).

**Body POST:**

| Campo | Reglas |
|-------|--------|
| `modulo` | Requerido; mismo `in` que generar PDF |
| `tipo` | Requerido; ej. `resumen_general`, máx. 50 |
| `estado` | Requerido; texto corto (ej. `Generado`, `Error 403`), máx. 100 |
| `descripcion` | Opcional, máx. 150 |

`cod_usuario` lo asigna el servidor desde el JWT.

**Cada elemento en `data`:** `id`, `modulo`, `tipo`, `estado`, `descripcion`, `created_at` (ISO 8601), `nombre_usuario`, y si la relación viene cargada `usuario: { nombre_usuario }`.

**Códigos:** 401 sin token; 422 validación en POST o query inválida; 403 en DELETE ajeno; 204 en DELETE OK.

**Nota:** La tabla `reportes` (PDF interno) y `reporte_registros` (historial para la UI) son independientes; el front no debe usar `localStorage` para este historial.

---

## 1. Ruta y método

| Método | Ruta completa (prefijo API) |
|--------|-----------------------------|
| **POST** | `{BASE}/api/v1/reportes/generar` |

Ejemplo local: `http://localhost:8000/api/v1/reportes/generar`

**Nombre de ruta Laravel:** `reportes.generar`

---

## 2. Cabeceras

| Cabecera | Valor |
|----------|--------|
| `Authorization` | `Bearer <token_jwt>` |
| `Content-Type` | `application/json` |
| `Accept` | `application/pdf` (recomendado; el servidor devuelve PDF) |

---

## 3. Cuerpo JSON (`ReporteRequest`)

### Campos raíz

| Campo | Tipo | Obligatorio | Reglas |
|-------|------|-------------|--------|
| `modulo` | string | Sí | Solo: `empleados`, `contratos`, `prestaciones`, `incapacidades`, `inasistencias`, `afiliaciones`, `disciplinario` |
| `tipo` | string | Sí | Máx. 50 caracteres |
| `params` | object | No | Objeto opcional con claves anidadas siguientes |

### `params` (todas opcionales)

| Clave | Tipo | Reglas |
|-------|------|--------|
| `params.cod_empleado` | integer | nullable; debe existir en `empleados.cod_empleado` |
| `params.cod_contrato` | integer | nullable; debe existir en `contrato.cod_contrato` |
| `params.tipo_certificacion` | string | nullable; máx. 30 |
| `params.descripcion` | string | nullable; máx. 150 (texto libre para historial / referencia) |

### Ejemplo mínimo

```json
{
  "modulo": "empleados",
  "tipo": "resumen_general"
}
```

### Ejemplo con parámetros opcionales

```json
{
  "modulo": "contratos",
  "tipo": "resumen_general",
  "params": {
    "cod_empleado": 12,
    "descripcion": "Corte mensual contratos — área RRHH"
  }
}
```

**Nota de implementación:** En `ReporteService`, los métodos por módulo **hoy no filtran** por `cod_empleado` ni `cod_contrato` al armar el PDF; igual se **validan** si los envías y `descripcion` **sí** se persiste en el registro del reporte. El front puede enviarlos para futuras extensiones o para trazabilidad en historial.

---

## 4. Respuesta exitosa (200)

- **Content-Type:** `application/pdf` (típico con DomPDF).
- **Cuerpo:** bytes del PDF (stream).
- **Content-Disposition:** incluye nombre tipo `reporte-{modulo}-YmdHis.pdf` (ver `ReporteController::generar`).

El PDF usa la vista `resources/views/reportes/general.blade.php` y toma la **primera empresa** (`Empresa::orderBy('id_empresa')->first()`) para encabezado (razón social, NIT, dirección).

---

## 5. Errores

| Código | Cuándo |
|--------|--------|
| **401** | Token ausente, inválido o expirado |
| **422** | Validación: `modulo` no permitido, `tipo` faltante o demasiado largo, `params.*` inválidos |
| **500** | Fallo interno (poco común en validación) |

Cuerpo 422 (Laravel estándar):

```json
{
  "message": "...",
  "errors": {
    "modulo": ["El módulo seleccionado no es válido."]
  }
}
```

---

## 6. Comportamiento por `modulo` (contenido actual del PDF)

Cada módulo genera un **resumen agregado** (títulos orientativos):

| `modulo` | Contenido resumido en el PDF |
|----------|------------------------------|
| `empleados` | Totales activos/retirados, distribución por estado, top profesiones |
| `contratos` | Totales por estado (ACTIVO/FINALIZADO), por tipo de contrato |
| `prestaciones` | Periodos, pendientes/pagados, totales por concepto (cesantías, intereses, prima, vacaciones) |
| `incapacidades` | Total, por tipo normativo, por entidad responsable |
| `inasistencias` | Total, justificadas/no, por mes |
| `afiliaciones` | Cobertura, ausencias EPS/ARL/pensión/etc., top por códigos EPS/ARL |
| `disciplinario` | Totales por tipo y estado, días de suspensión |

El servicio asigna un **código visible** en el payload del PDF tipo `RPT-000123` además del código temporal `EMP-…`, `CON-…`, etc. en la estructura interna.

---

## 7. Consumo desde el navegador (referencia)

### `fetch`

```javascript
const res = await fetch(`${API_BASE}/api/v1/reportes/generar`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    Accept: 'application/pdf',
  },
  body: JSON.stringify({ modulo: 'prestaciones', tipo: 'resumen_general' }),
});

if (!res.ok) {
  const err = await res.json().catch(() => ({}));
  throw err;
}

const blob = await res.blob();
const url = URL.createObjectURL(blob);
window.open(url, '_blank'); // o <a download>
```

### Axios

```javascript
const { data } = await axios.post(
  '/api/v1/reportes/generar',
  { modulo: 'empleados', tipo: 'resumen_general' },
  {
    headers: { Authorization: `Bearer ${token}` },
    responseType: 'blob',
  }
);
```

---

## 8. Archivos backend de referencia

- `routes/api.php` — ruta `POST reportes/generar`
- `app/Http/Controllers/ReporteController.php`
- `app/Http/Requests/ReporteRequest.php`
- `app/Services/ReporteService.php`
- `resources/views/reportes/general.blade.php`

---

## 9. Asistente (módulo `reportes` en el chat)

Para alinear el texto de ayuda del búho con este contrato, tras cambios en API conviene actualizar la entrada correspondiente en `database/data/chat_entradas_ayuda_seed.php` y volver a ejecutar `php artisan db:seed --class=ChatEntradaAyudaSeeder`.
