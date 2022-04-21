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
        'effective_date' => [
            'sometimes',
            'date_format:Y-m-d'
        ],
        'publish_after' => [
            'sometimes',
            'nullable',
            'date_format:Y-m-d'
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
        'percentage' => [
            'sometimes',
            'integer',
            'between:1,100'
        ]
    ],
    'messages' => [
        'total.regex' => 'item-type-allocated-expense/validation.total-regex',
        'currency_id.exists' => 'item-type-allocated-expense/validation.currency_id-exists'
    ]
];
