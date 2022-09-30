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
        'account' => [
            'required',
            'string',
            'max:255'
        ],
        'target_account' => [
            'sometimes',
            'string',
            'max:255',
            'nullable'
        ],
        'description' => [
            'sometimes',
            'string',
            'nullable'
        ],
        'amount' => [
            'required',
            'string',
            'regex:/^\d+\.\d{2}$/',
            'max:16'
        ],
        'currency_id' => [
            'required',
            'exists:currency,id'
        ],
        'category' => [
            'required',
            'string',
            Rule::in(['income', 'fixed', 'flexible', 'savings']),
        ],
        'start_date' => [
            'required',
            'date_format:Y-m-d'
        ],
        'end_date' => [
            'sometimes',
            'date_format:Y-m-d',
            'nullable'
        ],
        'disabled' => [
            'sometimes',
            'boolean'
        ],
        'frequency' => [
            'required',
            'json'
        ],
    ],
    'messages' => [
        'amount.regex' => 'item-type-budget/validation.total-regex',
        'currency_id.required' => 'item-type-budget/validation.currency_id-required',
        'currency_id.exists' => 'item-type-budget/validation.currency_id-exists'
    ]
];
