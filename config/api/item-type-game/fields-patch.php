<?php

declare(strict_types=1);

return [
    'game' => [
        'field' => 'game',
        'title' => 'item-type-game/fields.title-game',
        'description' => 'item-type-game/fields.description-game',
        'type' => 'json',
        'required' => true
    ],
    'statistics' => [
        'field' => 'statistics',
        'title' => 'item-type-game/fields.title-statistics',
        'description' => 'item-type-game/fields.description-statistics',
        'type' => 'json',
        'required' => false
    ],
    'winner' => [
        'field' => 'winner',
        'title' => 'item-type-game/fields.title-winner',
        'description' => 'item-type-game/fields.description-winner',
        'type' => 'string',
        'required' => false,
        'validation' => [
            'length' => 10
        ]
    ],
    'score' => [
        'field' => 'winner',
        'title' => 'item-type-game/fields.title-score',
        'description' => 'item-type-game/fields.description-score',
        'type' => 'integer',
        'required' => false
    ],
    'complete' => [
        'field' => 'complete',
        'title' => 'item-type-game/fields.title-complete',
        'description' => 'item-type-game/fields.description-complete',
        'type' => 'boolean',
        'required' => false
    ]
];