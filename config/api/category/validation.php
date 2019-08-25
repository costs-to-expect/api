<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'description' => 'required|string',
            'resource_type_id' => 'required|exists:resource_type,id'
        ],
        'messages' => [
            'name.unique' => 'category/validation.name-unique'
        ]
    ],
    'PATCH' => [
        'fields' => [
            'description' => 'sometimes|string'
        ],
        'messages' => [
            'name.unique' => 'category/validation.name-unique'
        ]
    ]
];
