<?php

return [
    'provider' => env('AI_PROVIDER', 'gemini'),

    'classification_confidence_threshold' => env('AI_CLASSIFICATION_CONFIDENCE_THRESHOLD', 0.75),

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model'   => env('GEMINI_MODEL', 'gemini-1.5-pro'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model'   => env('OPENAI_MODEL', 'gpt-4o'),
    ],
];
