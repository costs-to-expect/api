<?php

declare(strict_types=1);

return [
    'fields' => [
        'description' => [
            'sometimes',
            'string'
        ],
        'data' => [
            'sometimes',
            'json'
        ],
        'public' => [
            'sometimes',
            'boolean'
        ]
    ],
    'messages' => [
        'name.unique' => 'resource-type/validation.name-unique'
    ]
];
