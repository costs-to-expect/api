<?php

declare(strict_types=1);

return [
    'fields' => [
        'name' => [
            'sometimes',
            'string',
            'max:255'
        ],
        'description' => [
            'sometimes',
            'nullable',
            'string'
        ],
        'quantity' => [
            'sometimes',
            'integer',
            'min:0',
        ]
    ],
    'messages' => []
];
