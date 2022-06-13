<?php

declare(strict_types=1);

return [
    'include-categories' => [
        'parameter' => 'include-categories',
        'title' => 'item-type-allocated-expense/parameters.title-include-categories',
        'description' => 'item-type-allocated-expense/parameters.description-include-categories',
        'type' => 'boolean',
        'required' => false
    ],
    'include-subcategories' => [
        'parameter' => 'include-subcategories',
        'title' => 'item-type-allocated-expense/parameters.title-include-subcategories',
        'description' => 'item-type-allocated-expense/parameters.description-include-subcategories',
        'type' => 'boolean',
        'required' => false
    ],
    'include-unpublished' => [
        'parameter' => 'include-unpublished',
        'title' => 'item-type-allocated-expense/parameters.title-include-unpublished',
        'description' => 'item-type-allocated-expense/parameters.description-include-unpublished',
        'type' => 'boolean',
        'required' => false
    ],
    'category' => [
        "parameter" => "category",
        "title" => 'item-type-allocated-expense/parameters.title-category',
        "description" => 'item-type-allocated-expense/parameters.description-category',
        "default" => null,
        "type" => "string",
        "required" => false
    ],
    'subcategory' => [
        "parameter" => "subcategory",
        "title" => 'item-type-allocated-expense/parameters.title-subcategory',
        "description" => 'item-type-allocated-expense/parameters.description-subcategory',
        "default" => null,
        "type" => "string",
        "required" => false
    ],
    'year' => [
        "parameter" => "year",
        "title" => 'item-type-allocated-expense/parameters.title-year',
        "description" => 'item-type-allocated-expense/parameters.description-year',
        "default" => null,
        "type" => "integer",
        "required" => false
    ],
    'month' => [
        "parameter" => "month",
        "title" => 'item-type-allocated-expense/parameters.title-month',
        "description" => 'item-type-allocated-expense/parameters.description-month',
        "default" => null,
        "type" => "integer",
        "required" => false
    ]
];
