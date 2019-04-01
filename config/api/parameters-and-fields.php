<?php

$pagination = [
    'offset' => [
        'parameter' => 'offset',
        'title' => 'Offset',
        'description' => 'Record offset for pagination',
        'default' => 0,
        'type' => 'integer',
        'required' => false
    ],
    'limit' => [
        'parameter' => 'limit',
        'title' => 'Limit',
        'description' => 'Record limit for pagination',
        'default' => 10,
        'type' => 'integer',
        'required' => false
    ]
];

return [
    'sub_category' => [
        'fields' => [
            'name' => [
                'field' => 'name',
                'title' => 'Sub category name',
                'description' => 'Enter a name for the sub category',
                'type' => 'string',
                'required' => true
            ],
            'description' => [
                'field' => 'description',
                'title' => 'Sub category description',
                'description' => 'Enter a description for the sub category',
                'type' => 'string',
                'required' => true
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'description' => 'required|string'
                ],
                'messages' => [
                    'name.unique' => 'The sub category name has already been used within this category'
                ]
            ]
        ],
        'parameters' => [
            'collection' => [],
            'item' => []
        ]
    ],
    'item_category' => [
        'fields' => [
            'category_id' => [
                'field' => 'category_id',
                'title' => 'Category',
                'description' => 'Which category should the item be assigned to',
                'type' => 'string',
                'required' => true
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'category_id' => 'required|exists:category,id'
                ],
                'messages' => [
                    'category_id.required' => 'Category field required or could not be decoded',
                    'category_id.exists' => "Given category id does not exist"
                ]
            ]
        ],
        'parameters' => [
            'collection' => [],
            'item' => []
        ]
    ],
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
