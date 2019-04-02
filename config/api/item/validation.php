<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'description' => 'required|string',
            'effective_date' => 'required|date_format:Y-m-d',
            'total' => 'required|string|regex:/^\d+\.\d{2}$/',
            'percentage' => 'sometimes|required|integer|between:1,100'
        ],
        'messages' => [
            'total.regex' => 'item/validation.total-regex'
        ]
    ],
    'PATCH' => [
        'fields' => [
            'description' => 'sometimes|string',
            'effective_date' => 'sometimes|date_format:Y-m-d',
            'total' => 'sometimes|string|regex:/^\d+\.\d{2}$/',
            'percentage' => 'sometimes|integer|between:1,100'
        ],
        'messages' => [
            'total.regex' => 'item/validation.total-regex'
        ]
    ]
];
