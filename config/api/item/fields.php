<?php

declare(strict_types=1);

return [
    'description' => [
        'field' => 'description',
        'title' => 'item/fields.title-description',
        'description' => 'item/fields.description-description',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'effective_date' => [
        'field' => 'effective_date',
        'title' => 'item/fields.title-effective_date',
        'description' => 'item/fields.description-effective_date',
        'type' => 'date (yyyy-mm-dd)',
        'required' => true
    ],
    'publish_after' => [
        'field' => 'publish_after',
        'title' => 'item/fields.title-publish_after',
        'description' => 'item/fields.description-publish_after',
        'type' => 'date (yyyy-mm-dd)',
        'required' => false
    ],
    'total' => [
        'field' => 'total',
        'title' => 'item/fields.title-total',
        'description' => 'item/fields.description-total',
        'type' => 'decimal (10,2)',
        'required' => true
    ],
    'percentage' => [
        'field' => 'percentage',
        'title' => 'item/fields.title-percentage',
        'description' => 'item/fields.description-percentage',
        'type' => 'integer',
        'validation' => [
            'min' => 1,
            'max' => 100
        ],
        'required' => false
    ]
];
