<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'item-type-game/fields.title-name',
        'description' => 'item-type-game/fields.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-game/fields.title-description',
        'description' => 'item-type-game/fields.description-description',
        'type' => 'string',
        'required' => false
    ],
    'game' => [
        'field' => 'game',
        'title' => 'item-type-game/fields.title-game',
        'description' => 'item-type-game/fields.description-game',
        'type' => 'string',
        'required' => true
    ],
    'statistics' => [
        'field' => 'statistics',
        'title' => 'item-type-game/fields.title-statistics',
        'description' => 'item-type-game/fields.description-statistics',
        'type' => 'string',
        'required' => true
    ]
];
