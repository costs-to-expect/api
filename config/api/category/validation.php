<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'description' => [
                'required',
                'string'
            ]
        ],
        'messages' => [
            'name.unique' => 'category/validation.name-unique'
        ]
    ],
    'PATCH' => [
        'fields' => [
            'description' => [
                'sometimes',
                'string',
                'max:255'
            ]
        ],
        'messages' => [
            'name.unique' => 'category/validation.name-unique'
        ]
    ]
];
