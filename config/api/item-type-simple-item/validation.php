<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'name' =>
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'sometimes',
                'string'
            ],
            'quantity' => [
                'required',
                'integer',
                'min:0'
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
            'quantity' => [
                'sometimes',
                'integer',
                'min:0',
            ]
        ],
        'messages' => []
    ]
];
