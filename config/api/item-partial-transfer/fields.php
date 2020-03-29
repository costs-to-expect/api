<?php

declare(strict_types=1);

return [
    'resource_id' => [
        'field' => 'resource_id',
        'title' => 'item-partial-transfer/fields.title-resource_id',
        'description' => 'item-partial-transfer/fields.description-resource_id',
        'type' => 'string',
        'validation' => [
            'length' => 10
        ],
        'required' => true
    ],
    'percentage' => [
        'field' => 'percentage',
        'title' => 'item-partial-transfer/fields.title-percentage',
        'description' => 'item-partial-transfer/fields.description-percentage',
        'type' => 'integer',
        'validation' => [
            'min' => 1,
            'max' => 99
        ],
        'required' => true
    ]
];
