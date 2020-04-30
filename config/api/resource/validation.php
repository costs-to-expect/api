<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'description' => [
                'required',
                'string'
            ],
            'effective_date' => [
                'required',
                'date_format:Y-m-d'
            ]
        ],
        'messages' => [
            'name.unique' => 'resource/validation.name-unique',
            'effective_date.date_format' => 'resource/validation.effective_date-date_format'
        ]
    ],
    'PATCH' => [
        'fields' => [
            'description' => [
                'sometimes',
                'string'
            ],
            'effective_date' => [
                'sometimes',
                'date_format:Y-m-d'
            ]
        ],
        'messages' => [
            'name.unique' => 'resource/validation.name-unique',
            'effective_date.date_format' => 'resource/validation.effective_date-date_format'
        ]
    ]
];
