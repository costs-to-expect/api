<?php

declare(strict_types=1);

use Illuminate\Validation\Rule;

return [
    'fields' => [
        'name' => [
            'sometimes',
            'string',
            'max:255'
        ],
        'account' => [
            'sometimes',
            'string',
            'max:255'
        ],
        'target_account' => [
            'sometimes',
            'nullable',
            'string',
            'max:255'
        ],
        'description' => [
            'sometimes',
            'nullable',
            'string'
        ],
        'amount' => [
            'sometimes',
            'string',
            'regex:/^\d+\.\d{2}$/',
            'max:16'
        ],
        'category' => [
            'sometimes',
            'string',
            Rule::in(['income', 'fixed', 'flexible', 'savings']),
        ],
        'start_date' => [
            'sometimes',
            'date_format:Y-m-d'
        ],
        'end_date' => [
            'sometimes',
            'nullable',
            'date_format:Y-m-d'
        ],
        'disabled' => [
            'sometimes',
            'boolean'
        ],
        'frequency' => [
            'sometimes',
            'json'
        ],
    ],
    'messages' => [
        'amount.regex' => 'item-type-budget/validation.total-regex',
    ]
];
