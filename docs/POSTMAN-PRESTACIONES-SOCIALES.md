# Prestaciones Sociales – Pruebas en Postman

Base URL (con auth): `{{base_url}}/api/v1`  
Las rutas de prestaciones están dentro de `auth.api`; envía el token (cookie o header `Authorization: Bearer <token>`).

---

## 1. Resumen (dashboard)

**GET** `{{base_url}}/api/v1/prestaciones-sociales`

Devuelve totales pendientes (cesantías, intereses, prima, vacaciones) y lista de contratos vigentes para liquidación.

- **Headers:** (ninguno extra si usas cookie de login).
- **Body:** ninguno.

**Respuesta (200):**
```json
{
  "message": "Resumen de prestaciones sociales",
  "data": {
    "totales_pendientes": {
      "total_cesantias": 0,
      "total_intereses": 0,
      "total_prima": 0,
      "total_vacaciones": 0
    },
    "contratos_vigentes": [ ... ]
  }
}
```

---

## 2. Solo totales pendientes

**GET** `{{base_url}}/api/v1/prestaciones-sociales/totales`

**Respuesta (200):** `data` con `total_cesantias`, `total_intereses`, `total_prima`, `total_vacaciones`.

---

## 3. Contrato y sus prestaciones (historial)

**GET** `{{base_url}}/api/v1/contratos/{cod_contrato}/prestaciones`

Ejemplo: `GET .../contratos/1/prestaciones`

- **Body:** ninguno.

**Respuesta (200):**
```json
{
  "message": "Contrato y prestaciones",
  "data": {
    "contrato": { ... },
    "prestaciones": [ ... ]
  }
}
```

---

## 4. Calcular prestaciones (nuevo período)

**POST** `{{base_url}}/api/v1/contratos/{cod_contrato}/calcular-prestaciones`

Ejemplo: `POST .../contratos/1/calcular-prestaciones`

- **Body:** vacío (opcional `{}`).

El backend calcula desde el día siguiente al último `fecha_periodo_fin` (o desde `fecha_ingreso` del contrato si es la primera vez) hasta hoy, con fórmulas legales Colombia (cesantías, intereses 12%, prima semestral, vacaciones 15 días/año).

**Respuesta (201):**
```json
{
  "message": "Prestaciones calculadas correctamente",
  "data": {
    "cod_prestacion_social_periodo": 1,
    "cod_contrato": 1,
    "fecha_periodo_inicio": "2024-01-15",
    "fecha_periodo_fin": "2026-03-15",
    "dias_trabajados": 790,
    "salario_base": "2800000.00",
    "cesantias_valor": "...",
    "estado_pago": "Pendiente",
    ...
  }
}
```

**Errores (422):** "Contrato no encontrado", "Solo se pueden calcular prestaciones para contratos vigentes", "No hay días nuevos para calcular".

---

## 5. Cambiar estado (Pagar / Trasladar)

**POST** `{{base_url}}/api/v1/prestaciones-sociales/gestionar`

- **Headers:** `Content-Type: application/json`
- **Body (raw JSON):**
```json
{
  "cod_prestacion_social_periodo": 1,
  "estado_pago": "Pagado"
}
```

O para traslado al fondo:
```json
{
  "cod_prestacion_social_periodo": 1,
  "estado_pago": "Trasladado"
}
```

Solo períodos en estado **Pendiente** pueden cambiarse a **Pagado** o **Trasladado**.

**Respuesta (200):** `data` con el período actualizado.

**Errores (422):** "Estado de pago inválido", "Solo se puede cambiar el estado de períodos en estado Pendiente".

---

## 6. Eliminar período (solo Pendiente)

**DELETE** `{{base_url}}/api/v1/prestaciones-sociales/{cod_prestacion_social_periodo}`

Ejemplo: `DELETE .../prestaciones-sociales/3`

Solo se puede eliminar si `estado_pago` es **Pendiente**.

**Respuesta (200):** `{ "message": "Período eliminado correctamente" }`  
**Errores (422):** "Solo se pueden eliminar períodos en estado Pendiente". **(404):** Período no encontrado.

---

## 7. Listar todos los períodos

**GET** `{{base_url}}/api/v1/prestaciones-sociales/listar`

Devuelve todos los períodos con relación contrato, empleado y cargo.

---

## Orden sugerido para probar

1. **Login** (POST `/api/v1/login`) y guardar token/cookie.
2. **GET** `prestaciones-sociales` → ver totales y contratos vigentes.
3. **GET** `contratos/1/prestaciones` → ver contrato y períodos (tras seed puede haber datos).
4. **POST** `contratos/1/calcular-prestaciones` → crear nuevo período.
5. **GET** `contratos/1/prestaciones` → comprobar que aparece el nuevo período.
6. **POST** `prestaciones-sociales/gestionar` con `estado_pago: "Pagado"` para ese período.
7. **GET** `prestaciones-sociales/totales` → comprobar que ese período ya no suma en totales.
8. (Opcional) Crear otro período en Pendiente y **DELETE** `prestaciones-sociales/{id}` para probar eliminación.

---

## Datos de prueba (seed)

Tras `php artisan migrate:fresh --seed` tendrás:

- Contratos vigentes (cod_contrato 1 y 2) con empleados y cargos.
- Períodos de ejemplo en `prestacion_social_periodo` (PrestacionSocialPeriodoSeeder).

Puedes usar `cod_contrato = 1` o `2` en las URLs de contrato y los `cod_prestacion_social_periodo` que devuelva `GET contratos/1/prestaciones` para gestionar o eliminar.
