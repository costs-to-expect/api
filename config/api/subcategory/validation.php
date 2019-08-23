<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'description' => 'required|string'
        ],
        'messages' => [
            'name.unique' => 'subcategory/validation.name-unique'
        ]
    ],
    'PATCH' => [
        'fields' => [
            'description' => 'sometimes|string'
        ],
        'messages' => [
            'name.unique' => 'subcategory/validation.name-unique'
        ]
    ]
];
