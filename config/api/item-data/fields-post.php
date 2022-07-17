<?php

declare(strict_types=1);

return [
    'key' => [
        'field' => 'key',
        'title' => 'item-data/fields-post.title-key',
        'description' => 'item-data/fields-post.description-key',
        'type' => 'string',
        'validation' => [
            'unique-for' => 'item_id',
            'max-length' => 255
        ],
        'required' => true
    ],
    'value' => [
        'field' => 'value',
        'title' => 'item-data/fields-post.title-value',
        'description' => 'item-data/fields-post.description-value',
        'type' => 'json',
        'required' => true
    ]
];
