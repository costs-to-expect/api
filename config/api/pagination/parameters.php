<?php

declare(strict_types=1);

return [
    'offset' => [
        'parameter' => 'offset',
        'title' => 'pagination/parameters.title-offset',
        'description' => 'pagination/parameters.description-offset',
        'default' => 0,
        'type' => 'integer',
        'required' => false
    ],
    'limit' => [
        'parameter' => 'limit',
        'title' => 'pagination/parameters.title-limit',
        'description' => 'pagination/parameters.description-limit',
        'default' => 10,
        'type' => 'integer',
        'required' => false
    ]
];
