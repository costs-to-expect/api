<?php

declare(strict_types=1);

return [
    'collection' => [
        'include-unpublished' => [
            'field' => 'include-unpublished',
            'title' => 'item-type-allocated-expense/parameters.title-include-unpublished',
            'description' => 'item-type-allocated-expense/parameters.description-include-unpublished',
            'type' => 'boolean',
            'required' => false
        ],
        'year' => [
            "parameter" => "year",
            "title" => 'item-type-allocated-expense/parameters.title-year',
            "description" => 'item-type-allocated-expense/parameters.description-year',
            "default" => null,
            "type" => "integer",
            "required" => false
        ],
        'month' => [
            "parameter" => "month",
            "title" => 'item-type-allocated-expense/parameters.title-month',
            "description" => 'item-type-allocated-expense/parameters.description-month',
            "default" => null,
            "type" => "integer",
            "required" => false
        ]
    ],
    'item' => []
];
