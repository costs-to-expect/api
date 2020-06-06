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
            'total' => [
                'required',
                'string',
                'regex:/^\d+\.\d{2}$/',
                'max:16'
            ],
        ],
        'messages' => [
            'total.regex' => 'item-type-simple-expense/validation.total-regex'
        ]
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
            'total' => [
                'sometimes',
                'string',
                'regex:/^\d+\.\d{2}$/'
            ],
        ],
        'messages' => [
            'total.regex' => 'item-type-simple-expense/validation.total-regex'
        ]
    ]
];
