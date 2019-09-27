<?php

declare(strict_types=1);

return [
    'collection' => [
        'include-categories' => [
            'field' => 'include-categories',
            'title' => 'item/parameters.title-include-categories',
            'description' => 'item/parameters.description-include-categories',
            'type' => 'boolean',
            'required' => false
        ],
        'include-subcategories' => [
            'field' => 'include-subcategories',
            'title' => 'item/parameters.title-include-subcategories',
            'description' => 'item/parameters.description-include-subcategories',
            'type' => 'boolean',
            'required' => false
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
