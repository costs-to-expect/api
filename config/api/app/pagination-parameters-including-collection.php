<?php

declare(strict_types=1);

return [
    'offset' => [
        'parameter' => 'offset',
        'title' => 'app/pagination-parameters-including-collection.title-offset',
        'description' => 'app/pagination-parameters-including-collection.description-offset',
        'default' => 0,
        'type' => 'integer',
        'required' => false
    ],
    'limit' => [
        'parameter' => 'limit',
        'title' => 'app/pagination-parameters-including-collection.title-limit',
        'description' => 'app/pagination-parameters-including-collection.description-limit',
        'default' => 10,
        'type' => 'integer',
        'required' => false
    ],
    'collection' => [
        'parameter' => 'collection',
        'title' => 'app/pagination-parameters-including-collection.title-collection',
        'description' => 'app/pagination-parameters-including-collection.description-collection',
        'default' => false,
        'type' => 'boolean',
        'required' => false
    ]
];
