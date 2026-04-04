<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Edad mínima del empleado (fecha de nacimiento)
    |--------------------------------------------------------------------------
    |
    | En Colombia, el contrato de aprendizaje y el trabajo adolescente permiten
    | vincular a menores de 18 años a partir de los 15 años, con requisitos
    | (p. ej. autorización del Ministerio del Trabajo para 15–17 años según
    | normativa vigente, incl. reforma Ley 2466 de 2025). No bajar de 15 salvo
    | criterio jurídico explícito del proyecto.
    |
    */
    'empleado_edad_minima' => (int) env('EMPLEADO_EDAD_MINIMA', 15),

];
