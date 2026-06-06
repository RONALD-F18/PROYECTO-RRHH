# Paginación API — empleados y contratos

## Rutas (sin cambiar URL)

| Método | Ruta | Query |
|--------|------|--------|
| GET | `/api/v1/empleados` | `page` (default 1), `per_page` (default 25, máx 100) |
| GET | `/api/v1/contratos` | Igual |

## Respuesta

```json
{
  "message": "...",
  "data": [ /* solo la página actual */ ],
  "meta": {
    "current_page": 1,
    "per_page": 25,
    "total": 500,
    "last_page": 20
  }
}
```

## Breaking change (React)

- Antes: `response.data.data` era **todo** el listado.
- Ahora: `response.data.data` es **una página**; usar `response.data.meta` para paginador.
- `show`, `store`, `update`, `destroy` **no cambian**.

## Sin paginación (interno)

`GetAllEmpleados()` / `GetAllContratos()` siguen existiendo para **reportes PDF** (`ReporteService`).

## Ejemplos curl

```bash
curl -s "$BASE/empleados?page=1&per_page=25" -H "Authorization: Bearer TOKEN" -H "Accept: application/json"
curl -s "$BASE/contratos?page=2&per_page=10" -H "Authorization: Bearer TOKEN" -H "Accept: application/json"
```
