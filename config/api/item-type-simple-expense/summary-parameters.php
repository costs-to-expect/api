<?php

declare(strict_types=1);

return [
    'collection' => [
        'category' => [
            "parameter" => "category",
            "title" => 'item-type-simple-expense/summary-parameters.title-category',
            "description" => 'item-type-simple-expense/summary-parameters.description-category',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'categories' => [
            "parameter" => "categories",
            "title" => 'item-type-simple-expense/summary-parameters.title-categories',
            "description" => 'item-type-simple-expense/summary-parameters.description-categories',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ],
        'subcategory' => [
            "parameter" => "subcategory",
            "title" => 'item-type-simple-expense/summary-parameters.title-subcategory',
            "description" => 'item-type-simple-expense/summary-parameters.description-subcategory',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'subcategories' => [
            "parameter" => "subcategories",
            "title" => 'item-type-simple-expense/summary-parameters.title-subcategories',
            "description" => 'item-type-simple-expense/summary-parameters.description-subcategories',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ]
    ],
    'item' => []
];
