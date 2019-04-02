<?php

return [
    'item_sub_category' => [
        'fields' => [
            'sub_category_id' => [
                'field' => 'sub_category_id',
                'title' => 'Sub category',
                'description' => 'Which sub category should the item be assigned to',
                'type' => 'string',
                'required' => true
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [

                ],
                'messages' => [
                    'sub_category_id.required' => 'Sub category field required or could not be decoded',
                    'sub_category_id.exists' => "Given sub category id does not exist"
                ]
            ]
        ],
        'parameters' => [
            'collection' => [],
            'item' => []
        ]
    ],
    'request' => [
        'fields' => [
            'method' => [
                'field' => 'method',
                'title' => 'HTTP Verb',
                'description' => 'HTTP Verb for request',
                'type' => 'string',
                'required' => true
            ],
            'expected_status_code' => [
                'field' => 'expected_status_code',
                'title' => 'Expected HTTP status code',
                'description' => 'Enter the expected response HTTP status code',
                'type' => 'integer',
                'required' => true
            ],
            'returned_status_code' => [
                'field' => 'returned_status_code',
                'title' => 'Returned HTTP status code',
                'description' => 'Enter the returned response HTTP status code',
                'type' => 'integer',
                'required' => true
            ],
            'request_uri' => [
                'field' => 'request_uri',
                'title' => 'API request URI',
                'description' => 'Enter the URI the request was being made to',
                'type' => 'string',
                'required' => true
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'method' => 'required|string',
                    'expected_status_code' => 'required|integer|between:100,530',
                    'returned_status_code' => 'required|integer|between:100,530',
                    'request_uri' => 'required|string',
                ],
                'messages' => [

                ]
            ]
        ],
        'parameters' => [
            'collection' => [],
            'item' => []
        ]
    ]
];
