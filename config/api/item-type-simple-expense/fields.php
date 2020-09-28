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
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-simple-expense/fields.title-description',
        'description' => 'item-type-simple-expense/fields.description-description',
        'type' => 'string',
        'required' => false
    ],
    'currency_id' => [
        'field' => 'currency_id',
        'title' => 'item-type-simple-expense/fields.title-currency_id',
        'description' => 'item-type-simple-expense/fields.description-currency_id',
        'type' => 'string',
        'validation' => [
            'length' => 10
        ],
        'required' => true
    ],
    'total' => [
        'field' => 'total',
        'title' => 'item-type-simple-expense/fields.title-total',
        'description' => 'item-type-simple-expense/fields.description-total',
        'type' => 'decimal string (13,2)',
        'required' => true
    ]
];
