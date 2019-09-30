<?php

declare(strict_types=1);

return [
    'collection' => [
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
