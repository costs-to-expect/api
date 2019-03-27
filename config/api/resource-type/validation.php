<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'name' => 'required|string|unique:resource_type,name',
            'description' => 'required|string',
            'private' => 'sometimes|boolean',
        ],
        'messages' => [
            'name.unique' => 'The resource type has already been used'
        ]
    ]
];
