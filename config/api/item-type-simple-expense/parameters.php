<?php

declare(strict_types=1);

return [
    'collection' => [
        'include-categories' => [
            'parameter' => 'include-categories',
            'title' => 'item-type-simple-expense/parameters.title-include-categories',
            'description' => 'item-type-simple-expense/parameters.description-include-categories',
            'type' => 'boolean',
            'required' => false
        ],
        'include-subcategories' => [
            'parameter' => 'include-subcategories',
            'title' => 'item-type-simple-expense/parameters.title-include-subcategories',
            'description' => 'item-type-simple-expense/parameters.description-include-subcategories',
            'type' => 'boolean',
            'required' => false
        ],
        'category' => [
            "parameter" => "category",
            "title" => 'item-type-simple-expense/parameters.title-category',
            "description" => 'item-type-simple-expense/parameters.description-category',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'subcategory' => [
            "parameter" => "subcategory",
            "title" => 'item-type-simple-expense/parameters.title-subcategory',
            "description" => 'item-type-simple-expense/parameters.description-subcategory',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'year' => [
            "parameter" => "year",
            "title" => 'item-type-simple-expense/parameters.title-year',
            "description" => 'item-type-simple-expense/parameters.description-year',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'month' => [
            "parameter" => "month",
            "title" => 'item-type-simple-expense/parameters.title-month',
            "description" => 'item-type-simple-expense/parameters.description-month',
            "default" => null,
            "type" => "integer",
            "required" => false
        ]
    ],
    'item' => []
];
