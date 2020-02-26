<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string|max:255',
            'total' => 'required|string|regex:/^\d+\.\d{2}$/',
        ],
        'messages' => [
            'total.regex' => 'item-type-simple-expense/validation.total-regex'
        ]
    ],
    'PATCH' => [
        'fields' => [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'total' => 'sometimes|string|regex:/^\d+\.\d{2}$/',
        ],
        'messages' => [
            'total.regex' => 'item-type-simple-expense/validation.total-regex'
        ]
    ]
];
