<?php

declare(strict_types=1);

return [
    'min-length' => env('APP_HASH_MIN_LENGTH'),
    'category' => env('APP_HASH_SALT_CATEGORY'),
    'subcategory' => env('APP_HASH_SALT_SUBCATEGORY'),
    'resource-type' => env('APP_HASH_SALT_RESOURCE_TYPE'),
    'resource' => env('APP_HASH_SALT_RESOURCE'),
    'item' => env('APP_HASH_SALT_ITEM'),
    'item-category' => env('APP_HASH_SALT_ITEM_CATEGORY'),
    'item-log' => env('APP_HASH_SALT_ITEM_LOG'),
    'item-partial-transfer' => env('APP_HASH_SALT_ITEM_PARTIAL_TRANSFER'),
    'item-subcategory' => env('APP_HASH_SALT_ITEM_SUBCATEGORY'),
    'item-transfer' => env('APP_HASH_SALT_ITEM_TRANSFER'),
    'item-type' => env('APP_HASH_SALT_ITEM_TYPE'),
    'item-subtype' => env('APP_HASH_SALT_ITEM_SUBTYPE'),
    'permitted-user' => env('APP_HASH_SALT_PERMITTED_USER'),
    'user' => env('APP_HASH_SALT_USER'),
    'currency' => env('APP_HASH_SALT_CURRENCY'),
    'queue' => env('APP_HASH_SALT_QUEUE'),
    'forgot-password' => env('APP_HASH_SALT_FORGOT_PASSWORD'),
];
