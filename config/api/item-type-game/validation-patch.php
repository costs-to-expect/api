<?php

declare(strict_types=1);

return [
    'fields' => [
        'game' => [
            'sometimes',
            'json'
        ],
        'statistics' => [
            'sometimes',
            'json'
        ],
        'winner_id' => [
            'sometimes',
            'nullable',
            'exists:category,id'
        ],
        'score' => [
            'sometimes',
            'integer',
            'min:0'
        ],
        'complete' => [
            'sometimes',
            'boolean'
        ]
    ],
    'messages' => [
        'winner_id.exists' => 'item-type-game/validation.winner_id-exists'
    ]
];
