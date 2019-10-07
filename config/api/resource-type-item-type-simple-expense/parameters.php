<?php

declare(strict_types=1);

return [
    'collection' => [
        'include-categories' => [
            'field' => 'include-categories',
            'title' => 'resource-type-item/parameters.title-include-categories',
            'description' => 'resource-type-item/parameters.description-include-categories',
            'type' => 'boolean',
            'required' => false
        ],
        'include-subcategories' => [
            'field' => 'include-subcategories',
            'title' => 'resource-type-item/parameters.title-include-subcategories',
            'description' => 'resource-type-item/parameters.description-include-subcategories',
            'type' => 'boolean',
            'required' => false
        ],
        'year' => [
            "parameter" => "year",
            "title" => 'resource-type-item/parameters.title-year',
            "description" => 'resource-type-item/parameters.description-year',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'month' => [
            "parameter" => "month",
            "title" => 'resource-type-item/parameters.title-month',
            "description" => 'resource-type-item/parameters.description-month',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'category' => [
            "parameter" => "category",
            "title" => 'resource-type-item/parameters.title-category',
            "description" => 'resource-type-item/parameters.description-category',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'subcategory' => [
            "parameter" => "subcategory",
            "title" => 'resource-type-item/parameters.title-subcategory',
            "description" => 'resource-type-item/parameters.description-subcategory',
            "default" => null,
            "type" => "string",
            "required" => false
        ]
    ],
    'item' => []
];
