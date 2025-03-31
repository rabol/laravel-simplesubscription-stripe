<?php
// config for Laravel Simple Subscription Stripe

return [
    'stripe_key' => env('STRIPE_KEY'),
    'stripe_secret' => env('STRIPE_SECRET'),
    'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET'), // Web hook secret
    'stripe_webhook_tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300), // max time diff in webhook signature
    'stripe_webhook_url' => env('STRIPE_WEBHOOK_URL', env('APP_URL') . '/stripe/webhook') // webhook url
];
