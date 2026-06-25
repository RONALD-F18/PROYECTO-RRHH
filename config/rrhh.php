<?php

return [

    'empleado_edad_minima' => (int) env('EMPLEADO_EDAD_MINIMA', 15),

    'password_reset_url' => env('PASSWORD_RESET_URL'),

    'password_reset_return_url' => env('PASSWORD_RESET_RETURN_URL'),

    'password_reset_path' => env(
        'PASSWORD_RESET_PATH',
        '/reset-password.html'
    ),

    'contacto_email' => env('CONTACTO_EMAIL', 'ronaldacademy223@gmail.com'),

    'tipos_contrato' => [
        'Termino indefinido',
        'Termino fijo',
        'Obra o labor',
        'Aprendizaje',
        'Prestacion de servicios',
    ],

    'tipos_contrato_con_fecha_fin' => [
        'Termino fijo',
        'Obra o labor',
        'Aprendizaje',
    ],

    'formas_pago' => [
        'Mensual',
        'Quincenal',
        'Por hora',
    ],

    'modalidades_trabajo' => [
        'Presencial',
        'Remoto',
        'Hibrido',
    ],

    'horarios_trabajo' => [
        'Tiempo completo',
        'Medio tiempo',
        'Por turnos',
    ],

    'estados_incapacidad' => [
        'Activa',
        'Finalizada',
        'Cancelada',
    ],

    'estados_comunicacion' => [
        'EMITIDO',
        'NOTIFICADO',
    ],

    'tipos_comunicacion' => [
        'LLAMADO_VERBAL',
        'MEMORANDO',
        'FELICITACION',
    ],

    'motivos_comunicacion' => [
        'Incumplimiento',
        'Desacato',
        'Reincidencia',
        'Conducta',
        'Retraso',
    ],

    'estados_afiliacion' => [
        'Activa',
        'Inactiva',
        'Suspendida',
    ],

    'tipos_regimen' => [
        'Contributivo',
    ],

    'sexos_empleado' => [
        'Masculino',
        'Femenino',
        'Otro',
    ],

    'tipos_documento' => [
        'CC',
        'CE',
        'TI',
        'PASAPORTE',
    ],

    'justificado_inasistencia' => [
        'SI',
        'NO',
    ],

    'estados_prestacion_pago' => [
        'Pendiente',
        'Pagado',
        'Trasladado',
    ],

];
