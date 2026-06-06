<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Edad mínima del empleado (fecha de nacimiento)
    |--------------------------------------------------------------------------
    */
    'empleado_edad_minima' => (int) env('EMPLEADO_EDAD_MINIMA', 15),

    /*
    |--------------------------------------------------------------------------
    | Recuperación de contraseña
    |--------------------------------------------------------------------------
    |
    | PASSWORD_RESET_URL: página con formulario (public/reset-password.html).
    | Si está vacío, se usa APP_URL + /reset-password.html
    |
    | PASSWORD_RESET_RETURN_URL: enlace "Ir a iniciar sesión" tras éxito (front SPA).
    | Si está vacío, usa FRONTEND_URL.
    */
    'password_reset_url' => env('PASSWORD_RESET_URL'),

    'password_reset_return_url' => env('PASSWORD_RESET_RETURN_URL'),

    'password_reset_path' => env(
        'PASSWORD_RESET_PATH',
        '/reset-password.html'
    ),

];
