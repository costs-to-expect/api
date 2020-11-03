<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'sometimes',
                'string'
            ]
        ],
        'messages' => []
    ],
    'PATCH' => [
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
                'string',
                'size:10'
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
        'messages' => []
    ]
];
