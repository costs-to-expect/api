<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'name' => 'required|string|max:255|unique:resource_type,name',
            'description' => 'required|string|max:255',
            'private' => 'sometimes|boolean'
        ],
        'messages' => [
            'name.unique' => 'resource-type/validation.name-unique'
        ]
    ],
    'PATCH' => [
        'fields' => [
            'description' => 'sometimes|string|max:255',
            'private' => 'sometimes|boolean'
        ],
        'messages' => [
            'name.unique' => 'resource-type/validation.name-unique'
        ]
    ]
];
