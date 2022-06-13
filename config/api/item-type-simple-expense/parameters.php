<?php

declare(strict_types=1);

return [
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
    ]
];
