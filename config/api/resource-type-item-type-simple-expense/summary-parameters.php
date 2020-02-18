<?php

declare(strict_types=1);

return [
    'collection' => [
        'resources' => [
            "parameter" => "resources",
            "title" => 'resource-type-item-type-simple-expense/summary-parameters.title-resources',
            "description" => 'resource-type-item-type-simple-expense/summary-parameters.description-resources',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ],
        'category' => [
            "parameter" => "category",
            "title" => 'resource-type-item-type-simple-expense/summary-parameters.title-category',
            "description" => 'resource-type-item-type-simple-expense/summary-parameters.description-category',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'categories' => [
            "parameter" => "categories",
            "title" => 'resource-type-item-type-simple-expense/summary-parameters.title-categories',
            "description" => 'resource-type-item-type-simple-expense/summary-parameters.description-categories',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ],
        'subcategory' => [
            "parameter" => "subcategory",
            "title" => 'resource-type-item-type-simple-expense/summary-parameters.title-subcategory',
            "description" => 'resource-type-item-type-simple-expense/summary-parameters.description-subcategory',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'subcategories' => [
            "parameter" => "subcategories",
            "title" => 'resource-type-item-type-simple-expense/summary-parameters.title-subcategories',
            "description" => 'resource-type-item-type-simple-expense/summary-parameters.description-subcategories',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ]
    ],
    'item' => []
];
