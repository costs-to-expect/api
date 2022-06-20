<?php

declare(strict_types=1);

return [
    'enable' => (bool) env('APP_CACHE', true),
    'ttl' => 31536000,
    'public_key_prefix' => '-p-'
];
