<?php

declare(strict_types=1);

return [
    'fields' => [
        'description' => [
            'required',
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
