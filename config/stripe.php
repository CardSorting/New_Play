<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key give you access to Stripe's
    | API. The "publishable" key is typically used when interacting with
    | Stripe.js while the "secret" key accesses private API endpoints.
    |
    */
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    |
    | This determines the default currency used for payments. Stripe supports
    | many currencies, but we default to USD. You may change this to any
    | supported currency code.
    |
    */
    'currency' => env('STRIPE_CURRENCY', 'usd'),

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    |
    | Here you can configure various payment related settings such as minimum
    | amounts and payment methods to enable.
    |
    */
    'minimum_amount' => env('STRIPE_MINIMUM_AMOUNT', 0.50),
    'payment_methods' => ['card'],
    'automatic_payment_methods' => [
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | This section controls settings related to handling Stripe webhooks,
    | including the tolerance time for webhook signatures and handling of
    | duplicate events.
    |
    */
    'webhook_tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    'verify_webhooks' => env('STRIPE_VERIFY_WEBHOOKS', true),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching settings for idempotency keys and other Stripe-related
    | data. These help prevent duplicate charges and improve performance.
    |
    */
    'cache' => [
        'enabled' => true,
        'prefix' => 'stripe_',
        'ttl' => [
            'idempotency' => 24 * 60 * 60, // 24 hours
            'payment_intent' => 24 * 60 * 60,
            'webhook_event' => 24 * 60 * 60,
        ],
    ],
];