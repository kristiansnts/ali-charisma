<?php

$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOL);

return [

    'server_key' => env('MIDTRANS_SERVER_KEY'),

    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    'is_production' => $isProduction,

    'is_sanitized' => true,

    'is_3ds' => true,

    'payment_currency' => strtoupper((string) env('MIDTRANS_PAYMENT_CURRENCY', 'IDR')),

    'snap_js_url' => $isProduction
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',

    'urls' => [
        'finish' => env('MIDTRANS_FINISH_URL', env('APP_URL').'/checkout/finish'),
        'unfinish' => env('MIDTRANS_UNFINISH_URL', env('APP_URL').'/checkout/unfinish'),
        'error' => env('MIDTRANS_ERROR_URL', env('APP_URL').'/checkout/unfinish'),
    ],

];
