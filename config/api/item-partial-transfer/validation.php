<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'percentage' => [
                'required',
                'integer',
                'between:1,99'
            ]
        ],
        'messages' => [
            'resource_id.exists' => 'item-partial-transfer/validation.resource_id-exists'
        ]
    ]
];
