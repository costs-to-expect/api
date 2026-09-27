<?php

declare(strict_types=1);

use Illuminate\Validation\Rule;

return [
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
        'effective_date' => [
            'required',
            'date_format:Y-m-d'
        ],
        'publish_after' => [
            'sometimes',
            'date_format:Y-m-d'
        ],
        'currency_id' => [
            'required',
            'exists:currency,id'
        ],
        'total' => [
            'required',
            'string',
            'regex:/^\d+\.\d{2}$/',
            'max:16'
        ],
        'percentage' => [
            'sometimes',
            'integer',
            'between:1,100'
        ],
        'transaction_type' => [
            'required',
            'string',
            Rule::in(['expense', 'income']),
        ]
    ],
    'messages' => [
        'total.regex' => 'item-type-allocated-transaction/validation.total-regex',
        'currency_id.required' => 'item-type-allocated-transaction/validation.currency_id-required',
        'currency_id.exists' => 'item-type-allocated-transaction/validation.currency_id-exists'
    ]
];
