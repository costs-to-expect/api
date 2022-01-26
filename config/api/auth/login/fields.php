<?php

declare(strict_types=1);

return [
    'email' => [
        'field' => 'email',
        'title' => 'auth/login/fields.title-email',
        'description' => 'auth/login/fields.description-email',
        'type' => 'email',
        'required' => true
    ],
    'password' => [
        'field' => 'password',
        'title' => 'auth/login/fields.title-password',
        'description' => 'auth/login/fields.description-password',
        'type' => 'string',
        'validation' => [
            'min-length' => 12
        ],
        'required' => true
    ]
];
