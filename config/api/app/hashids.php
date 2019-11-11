<?php

declare(strict_types=1);

return [
    'min_length' => env('APP_HASH_MIN_LENGTH'),
    'category' => env('APP_HASH_SALT_CATEGORY'),
    'subcategory' => env('APP_HASH_SALT_SUBCATEGORY'),
    'resource_type' => env('APP_HASH_SALT_RESOURCE_TYPE'),
    'resource' => env('APP_HASH_SALT_RESOURCE'),
    'item' => env('APP_HASH_SALT_ITEM'),
    'item_category' => env('APP_HASH_SALT_ITEM_CATEGORY'),
    'item_subcategory' => env('APP_HASH_SALT_ITEM_SUBCATEGORY'),
    'item_type' => env('APP_HASH_SALT_ITEM_TYPE'),
    'permitted_user' => env('APP_HASH_SALT_PERMITTED_USER')
];
