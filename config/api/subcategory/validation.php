<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'description' => 'required|string|max:255'
        ],
        'messages' => [
            'name.unique' => 'subcategory/validation.name-unique'
        ]
    ],
    'PATCH' => [
        'fields' => [
            'description' =>
                [
                    'sometimes',
                    'string',
                    'max:255'
                ]
        ],
        'messages' => [
            'name.unique' => 'subcategory/validation.name-unique'
        ]
    ]
];
