<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'description' => [
                'required',
                'string'
            ],
            'data' => [
                'sometimes',
                'json'
            ]
        ],
        'messages' => [
            'name.unique' => 'resource/validation.name-unique',
        ]
    ],
    'PATCH' => [
        'fields' => [
            'description' => [
                'sometimes',
                'string'
            ],
            'data' => [
                'sometimes',
                'json'
            ]
        ],
        'messages' => [
            'name.unique' => 'resource/validation.name-unique',
        ]
    ]
];
