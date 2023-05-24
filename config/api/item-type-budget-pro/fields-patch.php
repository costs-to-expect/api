<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'item-type-budget-pro/fields-patch.title-name',
        'description' => 'item-type-budget-pro/fields-patch.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => false
    ],
    'account' => [
        'field' => 'account',
        'title' => 'item-type-budget-pro/fields-patch.title-account',
        'description' => 'item-type-budget-pro/fields-patch.description-account',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => false
    ],
    'target_account' => [
        'field' => 'target_account',
        'title' => 'item-type-budget-pro/fields-patch.title-target_account',
        'description' => 'item-type-budget-pro/fields-patch.description-target_account',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => false
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-budget-pro/fields-patch.title-description',
        'description' => 'item-type-budget-pro/fields-patch.description-description',
        'type' => 'string',
        'required' => false
    ],
    'amount' => [
        'field' => 'amount',
        'title' => 'item-type-budget-pro/fields-patch.title-amount',
        'description' => 'item-type-budget-pro/fields-patch.description-amount',
        'type' => 'decimal string (13,2)',
        'required' => false
    ],
    'category' => [
        'field' => 'category',
        'title' => 'item-type-budget-pro/fields-patch.title-category',
        'description' => 'item-type-budget-pro/fields-patch.description-category',
        'type' => 'string',
        'validation' => [
            'one-of' => [
                'income',
                'fixed',
                'flexible',
                'savings'
            ]
        ],
        'required' => true
    ],
    'start_date' => [
        'field' => 'start_date',
        'title' => 'item-type-budget-pro/fields-patch.title-start_date',
        'description' => 'item-type-budget-pro/fields-patch.description-start_date',
        'type' => 'date (yyyy-mm-dd)',
        'required' => true
    ],
    'end_date' => [
        'field' => 'end_date',
        'title' => 'item-type-budget-pro/fields-patch.title-end_date',
        'description' => 'item-type-budget-pro/fields-patch.description-end_date',
        'type' => 'date (yyyy-mm-dd)',
        'required' => false
    ],
    'disabled' => [
        'field' => 'disabled',
        'title' => 'item-type-budget-pro/fields-patch.title-disabled',
        'description' => 'item-type-budget-pro/fields-patch.description-disabled',
        'type' => 'boolean',
        'required' => false
    ],
    'frequency' => [
        'field' => 'frequency',
        'title' => 'item-type-budget-pro/fields-patch.title-frequency',
        'description' => 'item-type-budget-pro/fields-patch.description-frequency',
        'type' => 'json',
        'required' => true
    ]
];
