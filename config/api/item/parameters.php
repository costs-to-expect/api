<?php

declare(strict_types=1);

return [
    'collection' => [
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
        'sub_category' => [
            "parameter" => "sub_category",
            "title" => 'item/parameters.title-sub_category',
            "description" => 'item/parameters.description-sub_category',
            "default" => null,
            "type" => "string",
            "required" => false
        ]
    ],
    'item' => []
];
