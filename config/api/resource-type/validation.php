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
            ],
            'public' => [
                'sometimes',
                'boolean'
            ]
        ],
        'messages' => [
            'name.unique' => 'resource-type/validation.name-unique'
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
            ],
            'public' => [
                'sometimes',
                'boolean'
            ]
        ],
        'messages' => [
            'name.unique' => 'resource-type/validation.name-unique'
        ]
    ]
];
