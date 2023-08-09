<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'item-type-budget-pro/fields-post.title-name',
        'description' => 'item-type-budget-pro/fields-post.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'account' => [
        'field' => 'account',
        'title' => 'item-type-budget-pro/fields-post.title-account',
        'description' => 'item-type-budget-pro/fields-post.description-account',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'target_account' => [
        'field' => 'target_account',
        'title' => 'item-type-budget-pro/fields-post.title-target_account',
        'description' => 'item-type-budget-pro/fields-post.description-target_account',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => false
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-budget-pro/fields-post.title-description',
        'description' => 'item-type-budget-pro/fields-post.description-description',
        'type' => 'string',
        'required' => false
    ],
    'amount' => [
        'field' => 'amount',
        'title' => 'item-type-budget-pro/fields-post.title-amount',
        'description' => 'item-type-budget-pro/fields-post.description-amount',
        'type' => 'decimal string (13,2)',
        'required' => true
    ],
    'currency_id' => [
        'field' => 'currency_id',
        'title' => 'item-type-budget-pro/fields-post.title-currency_id',
        'description' => 'item-type-budget-pro/fields-post.description-currency_id',
        'type' => 'string',
        'validation' => [
            'length' => 10
        ],
        'required' => true
    ],
    'category' => [
        'field' => 'category',
        'title' => 'item-type-budget-pro/fields-post.title-category',
        'description' => 'item-type-budget-pro/fields-post.description-category',
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
        'title' => 'item-type-budget-pro/fields-post.title-start_date',
        'description' => 'item-type-budget-pro/fields-post.description-start_date',
        'type' => 'date (yyyy-mm-dd)',
        'required' => true
    ],
    'end_date' => [
        'field' => 'end_date',
        'title' => 'item-type-budget-pro/fields-post.title-end_date',
        'description' => 'item-type-budget-pro/fields-post.description-end_date',
        'type' => 'date (yyyy-mm-dd)',
        'required' => false
    ],
    'disabled' => [
        'field' => 'disabled',
        'title' => 'item-type-budget-pro/fields-post.title-disabled',
        'description' => 'item-type-budget-pro/fields-post.description-disabled',
        'type' => 'boolean',
        'required' => false
    ],
    'deleted' => [
        'field' => 'deleted',
        'title' => 'item-type-budget-pro/fields-post.title-deleted',
        'description' => 'item-type-budget-pro/fields-post.description-deleted',
        'type' => 'boolean',
        'required' => false
    ],
    'frequency' => [
        'field' => 'frequency',
        'title' => 'item-type-budget-pro/fields-post.title-frequency',
        'description' => 'item-type-budget-pro/fields-post.description-frequency',
        'type' => 'json',
        'required' => true
    ]
];
