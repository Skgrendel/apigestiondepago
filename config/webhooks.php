<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para los webhooks de tu aplicación
    |
    */

    'secret' => env('WEBHOOK_SECRET', 'your-secret-key'),

    'epayco_secret' => env('EPAYCO_WEBHOOK_SECRET', 'your-epayco-secret'),

    'events' => [
        'user.created',
        'user.updated',
        'user.deleted',
        'payment.completed',
        'payment.failed',
        'order.created',
        'order.updated',
        'order.cancelled',
    ],

    'timeout' => 30,

    'retry' => [
        'max_attempts' => 3,
        'delay' => 60, // segundos
    ],

    // Configuración específica de ePayco
    'epayco' => [
        'enabled' => env('EPAYCO_WEBHOOKS_ENABLED', true),
        'verify_signature' => env('EPAYCO_VERIFY_SIGNATURE', true),
        'payment_methods' => [
            'VS' => 'Crédito Visa',
            'MC' => 'Crédito Mastercard',
            'AM' => 'American Express',
            'DC' => 'Diners Club',
            'DV' => 'Débito Visa',
            'DM' => 'Débito Mastercard',
            'PSE' => 'PSE',
            'EF' => 'Efecty',
            'BA' => 'Baloto',
            'GA' => 'Gana',
            'SP' => 'SafetyPay',
            'SPF' => 'Split Payment',
            'COD' => 'Codensa',
            'TY' => 'Tuya',
            'EPM' => 'EPM',
        ],
    ],
];
