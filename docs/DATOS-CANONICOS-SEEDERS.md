# Datos canónicos (seeders + API) — aviso frontend

Tras `php artisan migrate:fresh --seed`, la BD y `GET /api/v1/catalogos` usan **exactamente** estos valores.

## Sexo empleado

```json
["Masculino", "Femenino", "Otro"]
```

- Enviar en POST/PATCH: `"sexo": "Masculino"` (Pascal case).
- PATCH parcial `{ "sexo": "Femenino" }` no revalida otros campos.

## Empleados demo

| Nombre | Documento | Correo | Sexo |
|---|---|---|---|
| Carlos Perez | 7954321012 | carlos.perez@gmail.com | Masculino |
| Ana Martinez | 5287654321 | ana.martinez@gmail.com | Femenino |

## Afiliaciones

- `estado_afiliacion`: `Activa` | `Inactiva` | `Suspendida`
- `tipo_regimen`: `Contributivo` (único)

## Incapacidades (estados en seed)

- `Activa`, `Finalizada`, `Cancelada`
- `cod_clasificacion_enfermedad` y `cod_tipo_incapacidad`: solo IDs de `/catalogos`

## Comunicaciones disciplinarias

- `tipo_comunicacion`: `LLAMADO_VERBAL` | `MEMORANDO` | `FELICITACION`
- `estado_comunicacion`: `EMITIDO` | `NOTIFICADO`
- `motivo_comunicacion`: texto libre (máx. 20 caracteres)
- Memorando: requiere `fecha_inicio_suspension`, `fecha_fin_suspension` y `dias_suspension` ≥ 1

## Usuarios demo (login)

| Usuario | Email | Contraseña | Rol |
|---|---|---|---|
| AdminRonald | ronaldacademy223@gmail.com | Ronaltix@7 | admin |
| AdminAngela | tatisg234p@gmail.com | Angela@8 | admin |
| Funcionario1 | ronalcrack222@gmail.com | Ronald1234 | funcionario |

## Regla frontend

No hardcodear enums distintos a `/catalogos`. El backend rechaza mayúsculas incorrectas en afiliaciones (`ACTIVA` → usar `Activa`).
