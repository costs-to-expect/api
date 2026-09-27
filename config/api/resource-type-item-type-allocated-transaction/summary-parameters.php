<?php

declare(strict_types=1);

return [
    'include-unpublished' => [
        'parameter' => 'include-unpublished',
        'title' => 'resource-type-item-type-allocated-transaction/summary-parameters.title-include-unpublished',
        'description' => 'resource-type-item-type-allocated-transaction/summary-parameters.description-include-unpublished',
        'type' => 'boolean',
        'required' => false
    ],
    'resources' => [
        "parameter" => "resources",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-resources',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-resources',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'year' => [
        "parameter" => "year",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-year',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-year',
        "default" => null,
        "type" => "integer",
        "required" => false
    ],
    'years' => [
        "parameter" => "years",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-years',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-years',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'month' => [
        "parameter" => "month",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-month',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-month',
        "default" => null,
        "type" => "integer",
        "required" => false
    ],
    'months' => [
        "parameter" => "months",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-months',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-months',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'category' => [
        "parameter" => "category",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-category',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-category',
        "default" => null,
        "type" => "string",
        "required" => false
    ],
    'categories' => [
        "parameter" => "categories",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-categories',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-categories',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'subcategory' => [
        "parameter" => "subcategory",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-subcategory',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-subcategory',
        "default" => null,
        "type" => "string",
        "required" => false
    ],
    'subcategories' => [
        "parameter" => "subcategories",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-subcategories',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-subcategories',
        "default" => false,
        "type" => "boolean",
        "required" => false
    ],
    'transaction_type' => [
        "parameter" => "transaction_type",
        "title" => 'resource-type-item-type-allocated-transaction/summary-parameters.title-transaction_type',
        "description" => 'resource-type-item-type-allocated-transaction/summary-parameters.description-transaction_type',
        "default" => null,
        "type" => "string",
        "required" => false
    ]
];
