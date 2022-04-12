<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'email' => [
                'required',
                'email'
            ]
        ],
        'messages' => [
            'email.permissible' => 'permitted-user/validation.email-permissible'
        ]
    ],
    'PATCH' => [
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
    ]
];
