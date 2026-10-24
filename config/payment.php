<?php

return [
    'provider' => env('PAYMENT_PROVIDER', 'stripe'),

    'stripe' => [
        'key'             => env('STRIPE_KEY'),
        'secret'          => env('STRIPE_SECRET'),
        'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET'),
    ],
];
