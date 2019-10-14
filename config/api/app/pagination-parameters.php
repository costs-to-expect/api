<?php

declare(strict_types=1);

return [
    'offset' => [
        'parameter' => 'offset',
        'title' => 'app/pagination-parameters.title-offset',
        'description' => 'app/pagination-parameters.description-offset',
        'default' => 0,
        'type' => 'integer',
        'required' => false
    ],
    'limit' => [
        'parameter' => 'limit',
        'title' => 'app/pagination-parameters.title-limit',
        'description' => 'app/pagination-parameters.description-limit',
        'default' => 10,
        'type' => 'integer',
        'required' => false
    ]
];
