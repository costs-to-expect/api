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
            'name.unique' => 'The resource name has already been used within this resource type',
            'effective_date.date_format' => 'The effective date does not match the required format yyyy-mm-dd (e.g. 2001-01-01)'
        ]
    ]
];
