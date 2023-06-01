<?php

declare(strict_types=1);

return [
    'game' => [
        'field' => 'game',
        'title' => 'item-type-game/fields-patch.title-game',
        'description' => 'item-type-game/fields-patch.description-game',
        'type' => 'json',
        'required' => false
    ],
    'statistics' => [
        'field' => 'statistics',
        'title' => 'item-type-game/fields-patch.title-statistics',
        'description' => 'item-type-game/fields-patch.description-statistics',
        'type' => 'json',
        'required' => false
    ],
    'winner_id' => [
        'field' => 'winner_id',
        'title' => 'item-type-game/fields-patch.title-winner_id',
        'description' => 'item-type-game/fields-patch.description-winner_id',
        'type' => 'string',
        'required' => false,
        'validation' => [
            'length' => 10
        ]
    ],
    'score' => [
        'field' => 'score',
        'title' => 'item-type-game/fields-patch.title-score',
        'description' => 'item-type-game/fields-patch.description-score',
        'type' => 'integer',
        'required' => false
    ],
    'complete' => [
        'field' => 'complete',
        'title' => 'item-type-game/fields-patch.title-complete',
        'description' => 'item-type-game/fields-patch.description-complete',
        'type' => 'boolean',
        'required' => false
    ]
];
