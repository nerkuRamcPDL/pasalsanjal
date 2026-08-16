<?php

declare(strict_types=1);

return [
    // SRS 9.1 #5 — bcrypt cost. Uses the same BCRYPT_ROUNDS env var Laravel's
    // own config/hashing.php reads, so there's exactly one source of truth
    // for the hashing cost rather than two settings that could drift apart.
    'bcrypt_cost' => (int) env('BCRYPT_ROUNDS', 12),

    'lockout_threshold' => (int) env('AUTH_LOCKOUT_THRESHOLD', 5),
    'lockout_minutes' => (int) env('AUTH_LOCKOUT_MINUTES', 15),
    'password_min_length' => (int) env('AUTH_PASSWORD_MIN_LENGTH', 10),

    'esewa_merchant_code' => env('ESEWA_MERCHANT_CODE'),
    'esewa_secret_key' => env('ESEWA_SECRET_KEY'),
    'khalti_secret_key' => env('KHALTI_SECRET_KEY'),
    'payment_sandbox' => env('PAYMENT_SANDBOX', true),
];
