<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'keycloak'),  // default to your custom guard
        'passwords' => 'users',
    ],

    'guards' => [
        'keycloak' => [
            'driver' => 'keycloak',
            'provider' => 'custom_users',   // <-- must match a provider defined below
        ],
        // ... other guards
    ],

    'providers' => [
        'custom_users' => [  // <-- This is your user provider key, matches guard's 'provider'
            'driver' => 'custom_user_provider', // <-- driver name registered in AuthServiceProvider
            'model' => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'custom_users',  // should also point to 'custom_users'
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
