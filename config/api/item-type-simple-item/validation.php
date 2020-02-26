<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string|max:255',
            'quantity' => 'required|integer|min:1',
        ],
        'messages' => []
    ],
    'PATCH' => [
        'fields' => [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer|min:1',
        ],
        'messages' => []
    ]
];
