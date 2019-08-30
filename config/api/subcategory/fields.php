<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'subcategory/fields.title-name',
        'description' => 'subcategory/fields.description-name',
        'type' => 'string',
        'validation' => [
            'unique-for' => 'category_id',
            'max-length' => 255
        ],
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'subcategory/fields.title-description',
        'description' => 'subcategory/fields.description-description',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ]
];
