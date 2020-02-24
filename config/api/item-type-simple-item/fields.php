<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'item-type-simple-item/fields.title-name',
        'description' => 'item-type-simple-item/fields.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => false
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-simple-item/fields.title-description',
        'description' => 'item-type-simple-item/fields.description-description',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => false
    ],
    'quantity' => [
        'field' => 'quantity',
        'title' => 'item-type-simple-item/fields.title-quantity',
        'description' => 'item-type-simple-item/fields.description-quantity',
        'type' => 'integer',
        'required' => true
    ]
];
