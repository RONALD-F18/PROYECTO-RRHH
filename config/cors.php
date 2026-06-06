<?php

$frontendOrigin = env('FRONTEND_URL') ? [rtrim(env('FRONTEND_URL'), '/')] : [];

return [

    /*
    | Permite que las rutas que empiezan con 'api/*' pasen por el filtro de CORS.
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
    | Orígenes permitidos (front local + producción).
    | FRONTEND_URL en .env añade el dominio del front desplegado.
    */
    'allowed_origins' => array_values(array_unique(array_merge(
        [
            'http://localhost:5173',
            'http://127.0.0.1:5173',
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'https://talentsphere.cloud',
            'https://www.talentsphere.cloud',
        ],
        $frontendOrigin
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', true),

];
