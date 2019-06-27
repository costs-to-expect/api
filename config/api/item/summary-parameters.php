<?php

declare(strict_types=1);

return [
    'collection' => [
        'include-unpublished' => [
            'field' => 'include-unpublished',
            'title' => 'item/summary-parameters.title-include-unpublished',
            'description' => 'item/summary-parameters.description-include-unpublished',
            'type' => 'boolean',
            'required' => false
        ],
        'year' => [
            "parameter" => "year",
            "title" => 'item/summary-parameters.title-year',
            "description" => 'item/summary-parameters.description-year',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'years' => [
            "parameter" => "years",
            "title" => 'item/summary-parameters.title-years',
            "description" => 'item/summary-parameters.description-years',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ],
        'month' => [
            "parameter" => "month",
            "title" => 'item/summary-parameters.title-month',
            "description" => 'item/summary-parameters.description-month',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'months' => [
            "parameter" => "months",
            "title" => 'item/summary-parameters.title-months',
            "description" => 'item/summary-parameters.description-months',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ],
        'category' => [
            "parameter" => "category",
            "title" => 'item/summary-parameters.title-category',
            "description" => 'item/summary-parameters.description-category',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'categories' => [
            "parameter" => "categories",
            "title" => 'item/summary-parameters.title-categories',
            "description" => 'item/summary-parameters.description-categories',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ],
        'subcategory' => [
            "parameter" => "subcategory",
            "title" => 'item/summary-parameters.title-subcategory',
            "description" => 'item/summary-parameters.description-subcategory',
            "default" => null,
            "type" => "string",
            "required" => false
        ],
        'subcategories' => [
            "parameter" => "subcategories",
            "title" => 'item/summary-parameters.title-subcategories',
            "description" => 'item/summary-parameters.description-subcategories',
            "default" => false,
            "type" => "boolean",
            "required" => false
        ]
    ],
    'item' => []
];
