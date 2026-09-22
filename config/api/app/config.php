<?php

declare(strict_types=1);

return [
    'admin_email' => env('ADMIN_EMAIL'),
    'registrations' => env('APP_REGISTRATIONS', false),
    'internal_api_key' => env('INTERNAL_API_KEY'),
    'token_expiry_days' => (int) env('API_TOKEN_EXPIRY_DAYS', 90),
];
