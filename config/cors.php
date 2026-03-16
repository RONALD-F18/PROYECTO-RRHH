<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Limita los métodos HTTP permitidos en lugar de permitir todos
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // Restringe los orígenes permitidos usando variables de entorno
    // Ajusta FRONTEND_URL y FRONTEND_URL_ALT en tu archivo .env
    'allowed_origins' => array_filter([
        env('FRONTEND_URL', null),
        env('FRONTEND_URL_ALT', null),
    ]),

    'allowed_origins_patterns' => [],

    // Limita los encabezados permitidos a los habituales
    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Origin',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    // Mantener en false evita enviar cookies/credenciales a cualquier origen
    'supports_credentials' => false,

];
