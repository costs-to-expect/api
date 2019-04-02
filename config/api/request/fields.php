<?php

declare(strict_types=1);

return [
    'method' => [
        'field' => 'method',
        'title' => 'request/fields.title-method',
        'description' => 'request/fields.description-method',
        'type' => 'string',
        'required' => true
    ],
    'expected_status_code' => [
        'field' => 'expected_status_code',
        'title' => 'request/fields.title-expected_status_code',
        'description' => 'request/fields.description-expected_status_code',
        'type' => 'integer',
        'required' => true
    ],
    'returned_status_code' => [
        'field' => 'returned_status_code',
        'title' => 'request/fields.title-returned_status_code',
        'description' => 'request/fields.description-returned_status_code',
        'type' => 'integer',
        'required' => true
    ],
    'request_uri' => [
        'field' => 'request_uri',
        'title' => 'request/fields.title-request_uri',
        'description' => 'request/fields.description-request_uri',
        'type' => 'string',
        'required' => true
    ]
];
