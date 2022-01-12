<?php

declare(strict_types=1);

return [
    'email' => [
        'field' => 'email',
        'title' => 'auth/register/fields.title-email',
        'description' => 'auth/register/fields.description-email',
        'type' => 'email',
        'required' => true
    ],
    'password' => [
        'field' => 'password',
        'title' => 'auth/register/fields.title-password',
        'description' => 'auth/register/fields.description-password',
        'type' => 'string',
        'validation' => [
            'min-length' => 12
        ],
        'required' => true
    ]
];
