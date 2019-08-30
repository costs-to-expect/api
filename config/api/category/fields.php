<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'category/fields.title-name',
        'description' => 'category/fields.description-name',
        'type' => 'string',
        'validation' => [
            'unique-for' => 'resource_type_id',
            'max-length' => 255
        ],
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'category/fields.title-description',
        'description' => 'category/fields.description-description',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'resource_type_id' => [
        'field' => 'resource_type_id',
        'title' => 'category/fields.title-resource_type_id',
        'description' => 'category/fields.description-resource_type_id',
        'type' => 'string',
        'required' => true
    ]
];
