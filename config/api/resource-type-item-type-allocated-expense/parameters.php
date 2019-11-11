<?php

declare(strict_types=1);

return [
    'collection' => [
        'include-categories' => [
            'parameter' => 'include-categories',
            'title' => 'resource-type-item-type-allocated-expense/parameters.title-include-categories',
            'description' => 'resource-type-item-type-allocated-expense/parameters.description-include-categories',
            'type' => 'boolean',
            'required' => false
        ],
        'include-subcategories' => [
            'parameter' => 'include-subcategories',
            'title' => 'resource-type-item-type-allocated-expense/parameters.title-include-subcategories',
            'description' => 'resource-type-item-type-allocated-expense/parameters.description-include-subcategories',
            'type' => 'boolean',
            'required' => false
        ],
        'include-unpublished' => [
            'parameter' => 'include-unpublished',
            'title' => 'resource-type-item-type-allocated-expense/parameters.title-include-unpublished',
            'description' => 'resource-type-item-type-allocated-expense/parameters.description-include-unpublished',
            'type' => 'boolean',
            'required' => false
        ],
        'year' => [
            "parameter" => "year",
            "title" => 'resource-type-item-type-allocated-expense/parameters.title-year',
            "description" => 'resource-type-item-type-allocated-expense/parameters.description-year',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'month' => [
            "parameter" => "month",
            "title" => 'resource-type-item-type-allocated-expense/parameters.title-month',
            "description" => 'resource-type-item-type-allocated-expense/parameters.description-month',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'category' => [
            "parameter" => "category",
            "title" => 'resource-type-item-type-allocated-expense/parameters.title-category',
            "description" => 'resource-type-item-type-allocated-expense/parameters.description-category',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'subcategory' => [
            "parameter" => "subcategory",
            "title" => 'resource-type-item-type-allocated-expense/parameters.title-subcategory',
            "description" => 'resource-type-item-type-allocated-expense/parameters.description-subcategory',
            "default" => null,
            "type" => "string",
            "required" => false
        ]
    ],
    'item' => []
];
