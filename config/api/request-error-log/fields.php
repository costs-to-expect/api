<?php

declare(strict_types=1);

return [
    'method' => [
        'field' => 'method',
        'title' => 'request-error-log/fields.title-method',
        'description' => 'request-error-log/fields.description-method',
        'type' => 'string',
        'required' => true
    ],
    'expected_status_code' => [
        'field' => 'expected_status_code',
        'title' => 'request-error-log/fields.title-expected_status_code',
        'description' => 'request-error-log/fields.description-expected_status_code',
        'type' => 'integer',
        'required' => true
    ],
    'returned_status_code' => [
        'field' => 'returned_status_code',
        'title' => 'request-error-log/fields.title-returned_status_code',
        'description' => 'request-error-log/fields.description-returned_status_code',
        'type' => 'integer',
        'required' => true
    ],
    'request_uri' => [
        'field' => 'request_uri',
        'title' => 'request-error-log/fields.title-request_uri',
        'description' => 'request-error-log/fields.description-request_uri',
        'type' => 'string',
        'required' => true
    ],
    'source' => [
        'field' => 'source',
        'title' => 'request-error-log/fields.title-source',
        'description' => 'request-error-log/fields.description-source',
        'type' => 'string',
        'required' => true
    ]
];
