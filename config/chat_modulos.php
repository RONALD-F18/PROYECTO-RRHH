<?php

/**
 * Catálogo de módulos para el asistente (GET /chat/ayuda).
 * Solo aparecen en el menú los que tengan al menos una entrada activa en chat_entradas_ayuda.
 * Claves deben coincidir con el campo `modulo` del seed.
 */
return [
    'definiciones' => [
        ['clave' => 'general', 'etiqueta' => 'Información general', 'descripcion' => 'Qué es Talent Sphere, mapa de módulos, acceso y buenas prácticas.', 'orden' => 10],
        ['clave' => 'empleados', 'etiqueta' => 'Empleados', 'descripcion' => 'Registro del colaborador, documento, estado ACTIVO/RETIRADO y enlace a usuario/portal.', 'orden' => 20],
        ['clave' => 'contratos', 'etiqueta' => 'Contratos', 'descripcion' => 'Ingreso, salario, auxilio y prestaciones.', 'orden' => 30],
        ['clave' => 'prestaciones_sociales', 'etiqueta' => 'Prestaciones sociales', 'descripcion' => 'Cesantías, prima, vacaciones, intereses y periodos.', 'orden' => 40],
        ['clave' => 'afiliaciones', 'etiqueta' => 'Afiliaciones', 'descripcion' => 'EPS, ARL, pensión, caja y fechas.', 'orden' => 50],
        ['clave' => 'incapacidades', 'etiqueta' => 'Incapacidades', 'descripcion' => 'Tipos, días, EPS/ARL y validaciones.', 'orden' => 60],
        ['clave' => 'catalogos_incapacidad', 'etiqueta' => 'Catálogos de incapacidad', 'descripcion' => 'Tipos y diagnóstico (CIE-10).', 'orden' => 70],
        ['clave' => 'catalogos_afiliacion', 'etiqueta' => 'Catálogos (EPS, bancos, cargos…)', 'descripcion' => 'Listas maestras para formularios.', 'orden' => 80],
        ['clave' => 'certificaciones', 'etiqueta' => 'Certificaciones', 'descripcion' => 'Certificados y constancias laborales.', 'orden' => 90],
        ['clave' => 'reportes', 'etiqueta' => 'Reportes', 'descripcion' => 'PDF por módulo e historial de generaciones.', 'orden' => 100],
        ['clave' => 'inasistencias', 'etiqueta' => 'Inasistencias', 'descripcion' => 'Registro de faltas.', 'orden' => 110],
        ['clave' => 'calendario', 'etiqueta' => 'Calendario', 'descripcion' => 'Actividades y eventos.', 'orden' => 120],
        ['clave' => 'disciplinarias', 'etiqueta' => 'Disciplinarias', 'descripcion' => 'Comunicaciones disciplinarias.', 'orden' => 130],
        ['clave' => 'empresas', 'etiqueta' => 'Empresas', 'descripcion' => 'Datos de razón social.', 'orden' => 140],
        ['clave' => 'autenticacion', 'etiqueta' => 'Inicio de sesión', 'descripcion' => 'Usuario, contraseña y sesión.', 'orden' => 150],
        ['clave' => 'administracion_usuarios', 'etiqueta' => 'Usuarios y roles', 'descripcion' => 'Administración de cuentas.', 'orden' => 160],
        ['clave' => 'asistente_chat', 'etiqueta' => 'Ayuda del chat', 'descripcion' => 'Conversaciones y uso del asistente.', 'orden' => 170],
    ],
    'sinonimos' => [
        'prestaciones_sociales' => ['cesantia', 'cesantias', 'prima', 'vacaciones', 'intereses'],
        'incapacidades' => ['incapacidad', 'eps', 'arl', 'maternidad', 'paternidad'],
        'afiliaciones' => ['afiliacion', 'afiliar', 'eps', 'pension', 'caja'],
        'contratos' => ['contrato', 'vinculacion', 'ingreso', 'salario'],
        'empleados' => ['empleado', 'funcionario', 'trabajador', 'personal', 'portal del empleado', 'portal empleado'],
        'certificaciones' => ['certificado', 'constancia', 'laboral'],
        'reportes' => ['reporte', 'informe', 'estadistica'],
        'autenticacion' => ['ingresar', 'sesion', 'acceso', 'contrasena'],
    ],
];
