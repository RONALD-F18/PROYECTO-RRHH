# Pruebas de la API en Postman

**Base:** `http://localhost:8000/api/v1`

---

## Antes de empezar: Login y cookie

El login devuelve el token en una **cookie**. Postman guarda esa cookie y la **envía sola** en las siguientes peticiones al mismo dominio (`http://localhost:8000`). Así se simula al cliente: **no hace falta agregar el header Authorization a mano**.

1. Haz **una vez** el **Login** (módulo Auth más abajo).
2. Las demás peticiones hazlas **contra la misma base** (`http://localhost:8000/api/v1/...`). Postman enviará la cookie automáticamente.
3. Si aun así recibes **401**, tu backend puede estar leyendo el token solo del header. Entonces en Headers agrega: **Authorization** = **Bearer** y pega el token (cópialo de la respuesta del login o de la cookie en Postman).

En todas las peticiones con Body usa **Body → raw → JSON** y **Content-Type: application/json** (Postman suele ponerlo al elegir JSON).

---

# Módulo Auth (sin cookie; son públicas)

## Login

- **Haz esto:** POST a `http://localhost:8000/api/v1/login`
- **Body (raw, JSON)** – pega esto:

```json
{
  "email_usuario": "ronaldacademy223@gmail.com",
  "contrasena_usuario": "Donald1234"
}
```

- **Qué ver:** 200, JSON con `message`, `role`, `user`. La cookie `token` queda guardada en Postman para el dominio.

---

## Recuperar contraseña (forgot-password)

- **Haz esto:** POST a `http://localhost:8000/api/v1/forgot-password`
- **Body (raw, JSON)** – pega esto:

```json
{
  "email_usuario": "ronaldacademy223@gmail.com"
}
```

- **Qué ver:** 200 y mensaje de que si el correo existe recibirás el enlace. Revisa correo o logs para el token.

---

## Restablecer contraseña (reset-password)

- **Haz esto:** POST a `http://localhost:8000/api/v1/reset-password`
- **Body (raw, JSON)** – cambia `TU_TOKEN_DEL_CORREO` por el token que te llegó:

```json
{
  "email_usuario": "ronaldacademy223@gmail.com",
  "token": "TU_TOKEN_DEL_CORREO",
  "contrasena_usuario": "NuevaClave123",
  "contrasena_usuario_confirmation": "NuevaClave123"
}
```

- **Qué ver:** 200 = contraseña actualizada. 422 = token inválido o expirado. Luego prueba Login con `NuevaClave123`.

---

## Logout (con cookie o token)

- **Haz esto:** POST a `http://localhost:8000/api/v1/logout`. No body. Misma URL base para que Postman envíe la cookie.
- **Qué ver:** 200 y mensaje de sesión cerrada.

---

# Módulo Cargos

Base: `http://localhost:8000/api/v1/cargos`  
Después del login, Postman envía la cookie solo; no hace falta poner Authorization si el backend la usa.

## Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/cargos`. Sin body.
- **Qué ver:** 200 y JSON con `message` y `data` (lista de cargos).

## Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/cargos`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nomb_cargo": "Supervisor de Operaciones",
  "descripcion": "Supervisa el área operativa."
}
```

- **Qué ver:** 201 y el cargo creado. 422 si validación falla.

## Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/cargos/1` (cambia `1` por el id que tengas). Sin body.
- **Qué ver:** 200 y el cargo. 404 si no existe.

## Actualizar

- **Haz esto:** PUT a `http://localhost:8000/api/v1/cargos/1` (cambia `1` por el id)
- **Body (raw, JSON)** – pega esto:

```json
{
  "nomb_cargo": "Supervisor de Operaciones y Logística",
  "descripcion": "Supervisa operaciones y logística."
}
```

- **Qué ver:** 200 y el cargo actualizado. 404 si no existe.

## Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/cargos/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

# Módulo Contratos

Base: `http://localhost:8000/api/v1/contratos`

## Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/contratos`. Sin body.
- **Qué ver:** 200 y JSON con lista de contratos.

## Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/contratos`  
  (Necesitas que existan empleado y cargo; con seeders suele ser `cod_empleado` y `cod_cargo` = 1.)
- **Body (raw, JSON)** – pega esto:

```json
{
  "tipo_contrato": "Contrato a término indefinido",
  "cod_empleado": 1,
  "forma_de_pago": "Mensual",
  "fecha_ingreso": "2025-01-15",
  "fecha_fin": null,
  "salario_base": 3200000,
  "cod_cargo": 1,
  "modalidad_trabajo": "Presencial",
  "horario_trabajo": "Lunes a viernes 8:00 a 17:00",
  "auxilio_transporte": true,
  "descripcion": "Contrato bajo normativa colombiana.",
  "estado_contrato": "ACTIVO"
}
```

- **Qué ver:** 201 y el contrato creado. 422 si validación falla o si `cod_empleado`/`cod_cargo` no existen.

## Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/contratos/1`. Sin body.
- **Qué ver:** 200 y el contrato. 404 si no existe.

## Actualizar

- **Haz esto:** PATCH a `http://localhost:8000/api/v1/contratos/1`
- **Body (raw, JSON)** – pega esto (puedes cambiar solo lo que quieras):

```json
{
  "tipo_contrato": "Contrato a término indefinido",
  "cod_empleado": 1,
  "forma_de_pago": "Mensual",
  "fecha_ingreso": "2025-01-15",
  "fecha_fin": null,
  "salario_base": 3500000,
  "cod_cargo": 1,
  "modalidad_trabajo": "Híbrido",
  "horario_trabajo": "Lunes a viernes 8:00 a 17:00",
  "auxilio_transporte": true,
  "descripcion": "Contrato actualizado.",
  "estado_contrato": "ACTIVO"
}
```

- **Qué ver:** 200 y el contrato actualizado. 404 si no existe.

## Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/contratos/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

# Módulo Empleados

Base: `http://localhost:8000/api/v1/empleados`

## Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/empleados`. Sin body.
- **Qué ver:** 200 y lista de empleados.

## Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/empleados`  
  Teléfono: 10 dígitos que empiecen por 3. Número de cuenta único. `cod_banco` que exista (ej. 1).
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_empleado": "Pedro",
  "apellidos_empleado": "Garcia Ruiz",
  "doc_iden": "9876543210",
  "tipo_documento": "CC",
  "fecha_nac": "1995-05-20",
  "direccion": "Calle 50 #10-20",
  "numero_telefono": "3151234567",
  "numero_cuenta": "123456789012",
  "tipo_cuenta": "AHORROS",
  "cod_banco": 1,
  "estado_emp": "ACTIVO",
  "discapacidad": "NINGUNA",
  "nacionalidad": "Colombiana",
  "estado_civil": "SOLTERO",
  "grupo_sanguineo": "O+",
  "profesion": "Ingeniero de Sistemas",
  "fec_exp_doc": "2018-03-15",
  "descripcion": "Nuevo empleado desarrollo."
}
```

- **Qué ver:** 201 y el empleado creado. 422 si validación falla (doc_iden, teléfono o cuenta duplicados; teléfono no 3xxxxxxxxx; etc.).

## Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/empleados/1`. Sin body.
- **Qué ver:** 200 y el empleado. 404 si no existe.

## Actualizar

- **Haz esto:** PATCH a `http://localhost:8000/api/v1/empleados/1`
- **Body (raw, JSON):** Mismos campos que crear; cambia solo los que quieras (ej. dirección, estado_civil).
- **Qué ver:** 200 y el empleado actualizado. 404 si no existe.

## Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/empleados/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

# Módulo Bancos

Base: `http://localhost:8000/api/v1/bancos`

## Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/bancos`. Sin body.
- **Qué ver:** 200 y lista de bancos.

## Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/bancos`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_banco": "Davivienda",
  "descripcion_banco": "Banco colombiano con amplia red."
}
```

- **Qué ver:** 201 y el banco creado. 422 si nombre duplicado o inválido.

## Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/bancos/1`. Sin body.
- **Qué ver:** 200 y el banco. 404 si no existe.

## Actualizar

- **Haz esto:** PUT a `http://localhost:8000/api/v1/bancos/1`
- **Body (raw, JSON)** – pega esto (o cambia texto):

```json
{
  "nombre_banco": "Davivienda",
  "descripcion_banco": "Banco colombiano con amplia red y servicios digitales."
}
```

- **Qué ver:** 200 y el banco actualizado. 404 si no existe.

## Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/bancos/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

# Módulo Afiliaciones – catálogos (tablas foráneas)

La tabla **afiliaciones** depende de estos catálogos. Primero debes tener datos en: **EPS**, **Riesgos**, **ARL**, **Fondos de pensiones**, **Fondos de cesantías**, **Caja de compensación** y **Empleados**. Con los seeders suelen quedar con id `1` en cada tabla. Prueba en este orden: cada catálogo (Obtener → Crear → Ver uno → Actualizar → Eliminar) y al final **Afiliaciones**.

---

## EPS

Base: `http://localhost:8000/api/v1/eps`

### Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/eps`. Sin body.
- **Qué ver:** 200 y lista de EPS.

### Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/eps`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_eps": "Salud Total EPS",
  "descripcion_eps": "EPS con cobertura nacional."
}
```

- **Qué ver:** 201 y la EPS creada. 422 si nombre duplicado.

### Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/eps/1`. Sin body.
- **Qué ver:** 200 y la EPS. 404 si no existe.

### Actualizar

- **Haz esto:** PUT o PATCH a `http://localhost:8000/api/v1/eps/1`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_eps": "Salud Total EPS",
  "descripcion_eps": "EPS con cobertura nacional e internacional."
}
```

- **Qué ver:** 200 y la EPS actualizada. 404 si no existe.

### Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/eps/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

## Riesgos

Base: `http://localhost:8000/api/v1/riesgos`

### Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/riesgos`. Sin body.
- **Qué ver:** 200 y lista de riesgos.

### Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/riesgos`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_riesgo": "Riesgo I",
  "descripcion_riesgo": "Riesgo mínimo según clasificación colombiana."
}
```

- **Qué ver:** 201 y el riesgo creado. 422 si nombre duplicado.

### Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/riesgos/1`. Sin body.
- **Qué ver:** 200 y el riesgo. 404 si no existe.

### Actualizar

- **Haz esto:** PUT o PATCH a `http://localhost:8000/api/v1/riesgos/1`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_riesgo": "Riesgo I",
  "descripcion_riesgo": "Riesgo mínimo, oficios administrativos."
}
```

- **Qué ver:** 200 y el riesgo actualizado. 404 si no existe.

### Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/riesgos/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

## ARL

Base: `http://localhost:8000/api/v1/arls`

### Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/arls`. Sin body.
- **Qué ver:** 200 y lista de ARL.

### Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/arls`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_arl": "Sura ARL",
  "descripcion_arl": "Administradora de riesgos laborales."
}
```

- **Qué ver:** 201 y la ARL creada. 422 si nombre duplicado.

### Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/arls/1`. Sin body.
- **Qué ver:** 200 y la ARL. 404 si no existe.

### Actualizar

- **Haz esto:** PUT o PATCH a `http://localhost:8000/api/v1/arls/1`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_arl": "Sura ARL",
  "descripcion_arl": "ARL con cobertura nacional."
}
```

- **Qué ver:** 200 y la ARL actualizada. 404 si no existe.

### Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/arls/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

## Pensiones (fondo de pensiones)

Base: `http://localhost:8000/api/v1/pensiones`

### Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/pensiones`. Sin body.
- **Qué ver:** 200 y lista de fondos de pensiones.

### Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/pensiones`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_fondo_pension": "Porvenir",
  "descripcion_fondo_pension": "Fondo de pensiones y cesantías."
}
```

- **Qué ver:** 201 y el fondo creado. 422 si nombre duplicado.

### Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/pensiones/1`. Sin body.
- **Qué ver:** 200 y el fondo. 404 si no existe.

### Actualizar

- **Haz esto:** PUT o PATCH a `http://localhost:8000/api/v1/pensiones/1`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_fondo_pension": "Porvenir",
  "descripcion_fondo_pension": "Fondo de pensiones con amplia red."
}
```

- **Qué ver:** 200 y el fondo actualizado. 404 si no existe.

### Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/pensiones/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

## Cesantías (fondo de cesantías)

Base: `http://localhost:8000/api/v1/cesantias`

### Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/cesantias`. Sin body.
- **Qué ver:** 200 y lista de fondos de cesantías.

### Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/cesantias`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_fondo_cesantia": "Protección",
  "descripcion_fondo_cesantia": "Fondo de cesantías."
}
```

- **Qué ver:** 201 y el fondo creado. 422 si nombre duplicado.

### Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/cesantias/1`. Sin body.
- **Qué ver:** 200 y el fondo. 404 si no existe.

### Actualizar

- **Haz esto:** PUT o PATCH a `http://localhost:8000/api/v1/cesantias/1`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre_fondo_cesantia": "Protección",
  "descripcion_fondo_cesantia": "Fondo de cesantías con cobertura nacional."
}
```

- **Qué ver:** 200 y el fondo actualizado. 404 si no existe.

### Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/cesantias/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

## Compensaciones (caja de compensación)

Base: `http://localhost:8000/api/v1/compensaciones`

### Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/compensaciones`. Sin body.
- **Qué ver:** 200 y lista de cajas de compensación.

### Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/compensaciones`
- **Body (raw, JSON)** – pega esto (el Request pide el campo `nombre`):

```json
{
  "nombre": "Colsubsidio",
  "descripcion_caja_compensacion": "Caja de compensación familiar."
}
```

- **Qué ver:** 201 y la caja creada. 422 si nombre duplicado.

### Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/compensaciones/1`. Sin body.
- **Qué ver:** 200 y la caja. 404 si no existe.

### Actualizar

- **Haz esto:** PUT o PATCH a `http://localhost:8000/api/v1/compensaciones/1`
- **Body (raw, JSON)** – pega esto:

```json
{
  "nombre": "Colsubsidio",
  "descripcion_caja_compensacion": "Caja de compensación con beneficios."
}
```

- **Qué ver:** 200 y la caja actualizada. 404 si no existe.

### Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/compensaciones/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

# Módulo Afiliaciones (tabla principal)

Base: `http://localhost:8000/api/v1/afiliaciones`

**Importante:** Una afiliación relaciona un **empleado** con una **EPS**, un **riesgo**, una **ARL**, un **fondo de pensiones**, un **fondo de cesantías** y una **caja de compensación**. Todos esos códigos deben existir. Tras los seeders suelen ser `1` en cada tabla. Asegúrate de tener al menos un empleado (ej. `cod_empleado = 1`).

## Obtener (listar)

- **Haz esto:** GET a `http://localhost:8000/api/v1/afiliaciones`. Sin body.
- **Qué ver:** 200 y lista de afiliaciones.

## Crear

- **Haz esto:** POST a `http://localhost:8000/api/v1/afiliaciones`  
  Usa IDs que existan (con seeders suelen ser 1 en cada tabla).
- **Body (raw, JSON)** – pega esto (ajusta los `cod_*` si en tu BD son otros):

```json
{
  "fecha_afiliacion_eps": "2024-01-15",
  "fecha_afiliacion_arl": "2024-01-15",
  "fecha_afiliacion_caja": "2024-01-15",
  "fecha_afiliacion_fondo_pensiones": "2024-01-15",
  "fecha_afiliacion_fondo_cesantias": "2024-01-15",
  "estado_afiliacion": "ACTIVA",
  "cod_eps": 1,
  "cod_riesgo": 1,
  "cod_arl": 1,
  "cod_fondo_pensiones": 1,
  "cod_fondo_cesantias": 1,
  "cod_caja_compensacion": 1,
  "cod_empleado": 1,
  "descripcion": "Afiliación inicial del empleado.",
  "tipo_regimen": "CONTRIBUTIVO"
}
```

- **Qué ver:** 201 y la afiliación creada. 422 si algún `cod_*` no existe o falla validación.

## Ver uno

- **Haz esto:** GET a `http://localhost:8000/api/v1/afiliaciones/1`. Sin body.
- **Qué ver:** 200 y la afiliación. 404 si no existe.

## Actualizar

- **Haz esto:** PUT o PATCH a `http://localhost:8000/api/v1/afiliaciones/1`
- **Body (raw, JSON)** – mismos campos; cambia fechas, estado o descripción si quieres:

```json
{
  "fecha_afiliacion_eps": "2024-01-15",
  "fecha_afiliacion_arl": "2024-01-15",
  "fecha_afiliacion_caja": "2024-01-15",
  "fecha_afiliacion_fondo_pensiones": "2024-01-15",
  "fecha_afiliacion_fondo_cesantias": "2024-01-15",
  "estado_afiliacion": "ACTIVA",
  "cod_eps": 1,
  "cod_riesgo": 1,
  "cod_arl": 1,
  "cod_fondo_pensiones": 1,
  "cod_fondo_cesantias": 1,
  "cod_caja_compensacion": 1,
  "cod_empleado": 1,
  "descripcion": "Afiliación actualizada.",
  "tipo_regimen": "CONTRIBUTIVO"
}
```

- **Qué ver:** 200 y la afiliación actualizada. 404 si no existe.

## Eliminar

- **Haz esto:** DELETE a `http://localhost:8000/api/v1/afiliaciones/1`. Sin body.
- **Qué ver:** 200 y mensaje de eliminación. 404 si no existe.

---

# Orden sugerido para probar todo

1. **Login** (Auth) → la cookie queda guardada.
2. **Cargos:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
3. **Contratos:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
4. **Empleados:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
5. **Bancos:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
6. **Catálogos de afiliaciones** (en cualquier orden, pero antes de Afiliaciones):
   - **EPS:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
   - **Riesgos:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
   - **ARL:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
   - **Pensiones:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
   - **Cesantías:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
   - **Compensaciones:** Obtener → Crear → Ver uno → Actualizar → (opcional) Eliminar.
7. **Afiliaciones:** Obtener → Crear (con cod_eps, cod_riesgo, cod_arl, cod_fondo_pensiones, cod_fondo_cesantias, cod_caja_compensacion, cod_empleado = 1 si usaste seeders) → Ver uno → Actualizar → (opcional) Eliminar.
8. **Logout** (Auth).
9. **Recuperar contraseña** y **Restablecer contraseña** cuando quieras (sin estar logueado).

Si en algún paso recibes **401**, comprueba que la URL sea la misma base (`http://localhost:8000`) para que Postman envíe la cookie; si tu backend solo acepta token en header, agrega **Authorization: Bearer &lt;token&gt;** en Headers.
