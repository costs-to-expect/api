<?php

declare(strict_types=1);

return [
    'offset' => [
        'parameter' => 'offset',
        'title' => 'Offset',
        'description' => 'Record offset for pagination',
        'default' => 0,
        'type' => 'integer',
        'required' => false
    ],
    'limit' => [
        'parameter' => 'limit',
        'title' => 'Limit',
        'description' => 'Record limit for pagination',
        'default' => 10,
        'type' => 'integer',
        'required' => false
    ]
];
