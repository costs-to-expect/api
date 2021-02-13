<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'resource/fields.title-name',
        'description' => 'resource/fields.description-name',
        'type' => 'string',
        'validation' => [
            'unique-for' => 'resource_type_id',
            'max-length' => 255
        ],
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'resource/fields.title-description',
        'description' => 'resource/fields.description-description',
        'type' => 'string',
        'required' => true
    ],
    'item_subtype_id' => [
        'field' => 'item_subtype_id',
        'title' => 'resource/fields.title-item_subtype_id',
        'description' => 'resource/fields.description-item_subtype_id',
        'type' => 'string',
        'validation' => [
            'length' => 10
        ],
        'required' => true
    ]
];
