<?php

declare(strict_types=1);

return [
    'password' => [
        'field' => 'password',
        'title' => 'auth/create-password/fields.title-password',
        'description' => 'auth/create-password/fields.description-password',
        'type' => 'string',
        'validation' => [
            'min-length' => 12
        ],
        'required' => true
    ],
    'password_confirmation' => [
        'field' => 'description',
        'title' => 'auth/create-password/fields.title-password_confirmation',
        'description' => 'auth/create-password/fields.description-password_confirmation',
        'type' => 'string',
        'validation' => [
            'matches' => 'password',
            'min-length' => 12
        ],
        'required' => true
    ]
];
