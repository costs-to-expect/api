<?php

declare(strict_types=1);

return [
    'fields' => [
        'message' => [
            'required',
            'string',
            'max:255'
        ],
        'parameters' => [
            'required',
            'json'
        ]
    ]
];
