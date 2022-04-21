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
        ]
    ],
    'messages' => [
        'name.unique' => 'resource/validation.name-unique',
    ]
];
