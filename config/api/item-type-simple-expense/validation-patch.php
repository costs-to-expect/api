<?php

declare(strict_types=1);

return [
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
        'currency_id' => [
            'sometimes',
            'exists:currency,id'
        ],
        'total' => [
            'sometimes',
            'string',
            'regex:/^\d+\.\d{2}$/'
        ],
    ],
    'messages' => [
        'total.regex' => 'item-type-simple-expense/validation.total-regex',
        'currency_id.exists' => 'item-type-simple-expense/validation.currency_id-exists'
    ]
];
