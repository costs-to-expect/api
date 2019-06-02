<?php

declare(strict_types=1);

return [
    'collection' => [
        'sort' => [
            "parameter" => "sort",
            "title" => 'sortable.title',
            "description" => 'sortable.description',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
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
            "title" => 'item/parameters.title-year',
            "description" => 'item/parameters.description-year',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'month' => [
            "parameter" => "month",
            "title" => 'item/parameters.title-month',
            "description" => 'item/parameters.description-month',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'category' => [
            "parameter" => "category",
            "title" => 'item/parameters.title-category',
            "description" => 'item/parameters.description-category',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'subcategory' => [
            "parameter" => "subcategory",
            "title" => 'item/parameters.title-subcategory',
            "description" => 'item/parameters.description-subcategory',
            "default" => null,
            "type" => "string",
            "required" => false
        ]
    ],
    'item' => []
];
