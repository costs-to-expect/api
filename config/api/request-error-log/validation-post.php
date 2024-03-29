<?php

declare(strict_types=1);

return [
    'fields' => [
        'method' => [
            'required',
            'string'
        ],
        'expected_status_code' => [
            'required',
            'integer',
            'between:100,530'
        ],
        'returned_status_code' => [
            'required',
            'integer',
            'between:100,530'
        ],
        'request_uri' => [
            'required',
            'string'
        ],
        'source' => [
            'required',
            'string',
            'in:website,api,app,legacy,postman'
        ],
        'debug' => [
            'sometimes',
            'string'
        ]
    ],
    'messages' => []
];
