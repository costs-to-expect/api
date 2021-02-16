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
    'data' => [
        'field' => 'data',
        'title' => 'resource/fields.title-data',
        'description' => 'resource/fields.description-data',
        'type' => 'json',
        'required' => false
    ],
];
