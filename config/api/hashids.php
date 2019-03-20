<?php

return [
    'min_length' => env('APP_HASH_MIN_LENGTH'),
    'category' => env('APP_HASH_SALT_CATEGORY'),
    'sub_category' => env('APP_HASH_SALT_SUB_CATEGORY'),
    'resource_type' => env('APP_HASH_SALT_RESOURCE_TYPE'),
    'resource' => env('APP_HASH_SALT_RESOURCE'),
    'item' => env('APP_HASH_SALT_ITEM'),
    'item_category' => env('APP_HASH_SALT_ITEM_CATEGORY'),
    'item_sub_category' => env('APP_HASH_SALT_ITEM_SUB_CATEGORY')
];
