<?php

declare(strict_types=1);

return [
    'include-unpublished' => [
        'parameter' => 'include-unpublished',
        'title' => 'item-type-allocated-transaction/summary-parameters.title-include-unpublished',
        'description' => 'item-type-allocated-transaction/summary-parameters.description-include-unpublished',
        'type' => 'boolean',
        'required' => false
    ],
    'year' => [
        "parameter" => "year",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-year',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-year',
        "default" => null,
        "type" => "integer",
        "required" => false
    ],
    'years' => [
        "parameter" => "years",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-years',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-years',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'month' => [
        "parameter" => "month",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-month',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-month',
        "default" => null,
        "type" => "integer",
        "required" => false
    ],
    'months' => [
        "parameter" => "months",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-months',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-months',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'category' => [
        "parameter" => "category",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-category',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-category',
        "default" => null,
        "type" => "string",
        "required" => false
    ],
    'categories' => [
        "parameter" => "categories",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-categories',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-categories',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'subcategory' => [
        "parameter" => "subcategory",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-subcategory',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-subcategory',
        "default" => null,
        "type" => "string",
        "required" => false
    ],
    'subcategories' => [
        "parameter" => "subcategories",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-subcategories',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-subcategories',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'transaction_type' => [
        "parameter" => "transaction_type",
        "title" => 'item-type-allocated-transaction/summary-parameters.title-transaction_type',
        "description" => 'item-type-allocated-transaction/summary-parameters.description-transaction_type',
        "default" => null,
        "type" => "string",
        "required" => false
    ]
];
