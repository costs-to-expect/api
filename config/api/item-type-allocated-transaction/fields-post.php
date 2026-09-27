<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'item-type-allocated-transaction/fields-post.title-name',
        'description' => 'item-type-allocated-transaction/fields-post.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-allocated-transaction/fields-post.title-description',
        'description' => 'item-type-allocated-transaction/fields-post.description-description',
        'type' => 'string',
        'required' => false
    ],
    'effective_date' => [
        'field' => 'effective_date',
        'title' => 'item-type-allocated-transaction/fields-post.title-effective_date',
        'description' => 'item-type-allocated-transaction/fields-post.description-effective_date',
        'type' => 'date (yyyy-mm-dd)',
        'required' => true
    ],
    'publish_after' => [
        'field' => 'publish_after',
        'title' => 'item-type-allocated-transaction/fields-post.title-publish_after',
        'description' => 'item-type-allocated-transaction/fields-post.description-publish_after',
        'type' => 'date (yyyy-mm-dd)',
        'required' => false
    ],
    'currency_id' => [
        'field' => 'currency_id',
        'title' => 'item-type-allocated-transaction/fields-post.title-currency_id',
        'description' => 'item-type-allocated-transaction/fields-post.description-currency_id',
        'type' => 'string',
        'validation' => [
            'length' => 10
        ],
        'required' => true
    ],
    'total' => [
        'field' => 'total',
        'title' => 'item-type-allocated-transaction/fields-post.title-total',
        'description' => 'item-type-allocated-transaction/fields-post.description-total',
        'type' => 'decimal string (13,2)',
        'required' => true
    ],
    'percentage' => [
        'field' => 'percentage',
        'title' => 'item-type-allocated-transaction/fields-post.title-percentage',
        'description' => 'item-type-allocated-transaction/fields-post.description-percentage',
        'type' => 'integer',
        'validation' => [
            'min' => 1,
            'max' => 100
        ],
        'required' => false
    ],
    'transaction_type' => [
        'field' => 'transaction_type',
        'title' => 'item-type-allocated-transaction/fields-post.title-transaction_type',
        'description' => 'item-type-allocated-transaction/fields-post.description-transaction_type',
        'type' => 'string',
        'validation' => [
            'one-of' => ['expense', 'income']
        ],
        'required' => true
    ]
];
