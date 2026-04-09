<?php

return [

    'openai_enabled' => filter_var(env('CHATBOT_OPENAI_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'openai_key' => env('OPENAI_API_KEY'),
    'openai_model' => env('CHATBOT_OPENAI_MODEL', 'gpt-4o-mini'),
    'openai_timeout' => (int) env('CHATBOT_OPENAI_TIMEOUT', 45),

    /** Últimos mensajes enviados al modelo como contexto (usuario + asistente). */
    'max_mensajes_contexto' => (int) env('CHATBOT_MAX_CONTEXT_MESSAGES', 12),
];
