<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'item-type-simple-expense/fields.title-name',
        'description' => 'item-type-simple-expense/fields.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => false
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-simple-expense/fields.title-description',
        'description' => 'item-type-simple-expense/fields.description-description',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => false
    ],
    'total' => [
        'field' => 'total',
        'title' => 'item-type-simple-expense/fields.title-total',
        'description' => 'item-type-simple-expense/fields.description-total',
        'type' => 'decimal (10,2)',
        'required' => true
    ]
];
