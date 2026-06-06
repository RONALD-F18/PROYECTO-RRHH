# Dashboard — `GET /api/v1/dashboard/resumen`

Endpoint **nuevo**. Los listados existentes (`empleados`, `contratos`, etc.) **no cambian**.

## Autenticación

`Authorization: Bearer {access_token}` (mismo login Sanctum que el resto de la API).

## Ejemplo curl

```bash
BASE="https://mi-api.lemonforest-f9c17ad2.eastus.azurecontainerapps.io/api/v1"

# Login
curl -s -X POST "$BASE/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email_usuario":"...","contrasena_usuario":"..."}'

# Resumen (sustituye los 7 GET del panel)
curl -s "$BASE/dashboard/resumen" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json"
```

## Respuesta (`200`)

```json
{
  "message": "Resumen del dashboard obtenido exitosamente",
  "data": {
    "empleados_activos": 0,
    "contratos_vigentes": 0,
    "contratos_otros": 0,
    "inasistencias_mes_actual": 0,
    "incapacidades_total": 0,
    "afiliaciones_total": 0,
    "certificaciones_total": 0,
    "inasistencias_ultimos_6_meses": [
      { "clave": "2025-12", "etiqueta": "Dic", "total": 0 }
    ],
    "contratos_pie": [
      { "name": "Vigentes", "value": 0 },
      { "name": "Finalizados u otros", "value": 0 }
    ],
    "actividades_recientes": [
      {
        "titulo": "...",
        "tipo_actividad": "...",
        "estado": "COMPLETADA",
        "fecha_inicio": "2026-05-01",
        "fecha_creacion": "2026-04-28",
        "prioridad": null
      }
    ]
  }
}
```

## Criterios de conteo (alineados con el front)

| Campo | Regla |
|-------|--------|
| `empleados_activos` | `estado_emp = ACTIVO` |
| `contratos_vigentes` | `estado_contrato = ACTIVO` |
| `contratos_otros` | `estado_contrato != ACTIVO` |
| `inasistencias_mes_actual` | `fecha_inasistencia` en el mes calendario actual |
| `incapacidades_total` | `COUNT(*)` en tabla `incapacidad` |
| `afiliaciones_total` | `COUNT(*)` en `afiliaciones` |
| `certificaciones_total` | `COUNT(*)` en `certificaciones` |
| `inasistencias_ultimos_6_meses` | 6 meses (actual − 5 … actual), etiquetas `Ene`…`Dic` |
| `contratos_pie` | Mismos totales que vigentes / otros |
| `actividades_recientes` | Últimas 6 por `fecha_creacion` desc; `tipo` → `tipo_actividad` |

## Cambio en React (solo dashboard)

En `dashboardResumen.js`, reemplazar los 7 `GET` de listados por **uno**:

```js
const { data } = await api.get('/dashboard/resumen');
return data.data; // o el shape que ya use Panel.jsx
```

El resto de módulos sigue usando los endpoints actuales.
