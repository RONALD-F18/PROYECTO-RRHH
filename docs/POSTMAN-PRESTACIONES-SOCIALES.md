# Prestaciones Sociales – Pruebas en Postman

Base URL (con auth): `{{base_url}}/api/v1`  
Las rutas de prestaciones están dentro de `auth.api`; envía el token (cookie o header `Authorization: Bearer <token>`).

**Los cálculos son 100% automáticos:** al llamar **POST calcular-prestaciones** no envías montos; el backend calcula cesantías, intereses, prima y vacaciones con las fórmulas legales (360 días, 12% intereses, prima semestral, vacaciones/720).

---

## Cómo validar que todo funcione bien (Postman)

Sigue este orden. Reemplaza `http://localhost:8000` por tu base si usas otra.

### Paso 0: Login (obtener sesión)

- **POST** `http://localhost:8000/api/v1/login`
- **Body** (raw, JSON):
```json
{
  "email_usuario": "tu_email@ejemplo.com",
  "contrasena_usuario": "tu_clave"
}
```
- Si la API usa cookie, Postman guarda la cookie y la enviará en las siguientes peticiones al mismo dominio.
- Si recibes 401 en los pasos siguientes: en **Headers** agrega **Authorization** = **Bearer** y pega el token (puedes verlo en la pestaña Cookies de Postman después del login, o si tu login devuelve el token en el JSON úsalo ahí).

---

### Paso 1: Pantalla principal (resumen)

- **GET** `http://localhost:8000/api/v1/prestaciones-sociales`
- **Qué validar:** Status 200. En `data` deben existir `totales_pendientes` (cuatro totales numéricos) y `contratos_vigentes` (array). Anota un `cod_contrato` que aparezca (ej. 1 o 2).

---

### Paso 2: Ver contrato y sus prestaciones

- **GET** `http://localhost:8000/api/v1/contratos/1/prestaciones` (usa el `cod_contrato` que tengas).
- **Qué validar:** Status 200. `data.contrato` con empleado y cargo. `data.prestaciones` es un array (puede estar vacío si aún no hay períodos calculados).

---

### Paso 3: Calcular prestaciones (cálculo automático)

- **POST** `http://localhost:8000/api/v1/contratos/1/calcular-prestaciones`
- **Body:** vacío o `{}`.
- **Qué validar:**
  - Status **201**.
  - En `data`: `cod_prestacion_social_periodo`, `cod_contrato`, `fecha_periodo_inicio`, `fecha_periodo_fin`, `dias_trabajados`, `salario_base`, `auxilio_transporte`, `cesantias_valor`, `intereses_cesantias_valor`, `prima_valor`, `vacaciones_valor`, `estado_pago` = `"Pendiente"`.
  - **Comprobar que los números tienen sentido:** con el `salario_base` y `dias_trabajados` del contrato, cesantías ≈ (salario+auxilio)*días/360; intereses ≈ cesantías*0.12*(días/360); vacaciones ≈ salario*días/720. No hace falta recalcular a mano; con que vengan valores coherentes y no cero (si hay días) es suficiente.

Si recibes **422** "No hay días nuevos para calcular" es porque ya existe un período hasta hoy; en ese caso usa otro contrato o elimina ese período (solo si está Pendiente) con DELETE (paso 7) y vuelve a calcular.

---

### Paso 4: Comprobar que el período quedó guardado

- **GET** `http://localhost:8000/api/v1/contratos/1/prestaciones`
- **Qué validar:** En `data.prestaciones` debe aparecer el período recién creado (mismo `cod_prestacion_social_periodo` y fechas). Anota ese `cod_prestacion_social_periodo` para el siguiente paso.

---

### Paso 5: Totales pendientes (debe incluir el nuevo período)

- **GET** `http://localhost:8000/api/v1/prestaciones-sociales/totales`
- **Qué validar:** Los totales deben reflejar al menos el período que acabas de crear (si era el único pendiente, cada total coincidirá con el valor de ese período).

---

### Paso 6: Gestionar – marcar como Pagado

- **POST** `http://localhost:8000/api/v1/prestaciones-sociales/gestionar`
- **Headers:** `Content-Type: application/json`
- **Body** (raw, JSON), usando el `cod_prestacion_social_periodo` del paso 4:
```json
{
  "cod_prestacion_social_periodo": 1,
  "estado_pago": "Pagado"
}
```
- **Qué validar:** Status 200. En `data` el período debe tener `estado_pago` = `"Pagado"` y `fecha_pago_cancelacion` con fecha.

---

### Paso 7: Totales ya no deben incluir ese período

- **GET** `http://localhost:8000/api/v1/prestaciones-sociales/totales`
- **Qué validar:** Si ese era el único período pendiente, los totales bajan (o pasan a 0). Así confirmas que solo se suman los que están en **Pendiente**.

---

### Paso 8 (opcional): Listar todos los períodos

- **GET** `http://localhost:8000/api/v1/prestaciones-sociales/listar`
- **Qué validar:** Status 200. Array con todos los períodos (incluido el que marcaste Pagado), con contrato, empleado y cargo.

---

### Paso 9 (opcional): Eliminar un período Pendiente

- Crea otro período con **POST** `contratos/1/calcular-prestaciones` (si ya no hay días nuevos, usa otro contrato).
- **DELETE** `http://localhost:8000/api/v1/prestaciones-sociales/{cod_prestacion_social_periodo}` (el ID del período en Pendiente).
- **Qué validar:** Status 200, mensaje "Período eliminado correctamente". Luego **GET** `contratos/1/prestaciones` y ese período ya no debe aparecer.

---

Si todos los pasos dan el status esperado y los totales se actualizan al pagar, el flujo y los cálculos automáticos están funcionando bien.

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
