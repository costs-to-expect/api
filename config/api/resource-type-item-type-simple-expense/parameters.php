<?php

declare(strict_types=1);

return [
    'collection' => [
        'include-categories' => [
            'parameter' => 'include-categories',
            'title' => 'resource-type-item-type-simple-expense/parameters.title-include-categories',
            'description' => 'resource-type-item-type-simple-expense/parameters.description-include-categories',
            'type' => 'boolean',
            'required' => false
        ],
        'include-subcategories' => [
            'parameter' => 'include-subcategories',
            'title' => 'resource-type-item-type-simple-expense/parameters.title-include-subcategories',
            'description' => 'resource-type-item-type-simple-expense/parameters.description-include-subcategories',
            'type' => 'boolean',
            'required' => false
        ],
        'category' => [
            "parameter" => "category",
            "title" => 'resource-type-item-type-simple-expense/parameters.title-category',
            "description" => 'resource-type-item-type-simple-expense/parameters.description-category',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'subcategory' => [
            "parameter" => "subcategory",
            "title" => 'resource-type-item-type-simple-expense/parameters.title-subcategory',
            "description" => 'resource-type-item-type-simple-expense/parameters.description-subcategory',
            "default" => null,
            "type" => "string",
            "required" => false
        ]
    ],
    'item' => []
];
