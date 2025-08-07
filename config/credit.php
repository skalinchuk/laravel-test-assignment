<?php

return [
    'age' => [
        'min' => env('CREDIT_MIN_AGE', 18),
        'max' => env('CREDIT_MAX_AGE', 60),
    ],

    'score' => [
        'minimum' => env('CREDIT_MIN_SCORE', 500),
    ],

    'income' => [
        'minimum' => env('CREDIT_MIN_INCOME', 1000),
    ],

    'regions' => [
        'allowed' => explode(',', env('CREDIT_ALLOWED_REGIONS', 'PR,BR,OS')),
    ],

    'features' => [
        'prague_random_rejection' => env('CREDIT_PRAGUE_RANDOM_REJECTION', true),
        'notifications_enabled' => env('CREDIT_NOTIFICATIONS_ENABLED', true),
    ],

    'adjustments' => [
        'ostrava_rate_increase' => env('CREDIT_OSTRAVA_RATE_INCREASE', 5.0),
    ],
];
