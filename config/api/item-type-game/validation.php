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
            ],
            'game' => [
                'required',
                'json'
            ],
            'statistics' => [
                'required',
                'json'
            ]
        ],
        'messages' => []
    ],
    'PATCH' => [
        'fields' => [
            'name' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string'
            ],
            'game' => [
                'required',
                'json'
            ],
            'statistics' => [
                'required',
                'json'
            ]
        ],
        'messages' => []
    ]
];
