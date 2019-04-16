<?php

declare(strict_types=1);

return [
    'collection' => [
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
        ]
    ],
    'item' => []
];
