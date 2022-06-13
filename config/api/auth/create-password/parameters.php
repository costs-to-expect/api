<?php

declare(strict_types=1);

return [
    'token' => [
        'field' => 'token',
        'title' => 'auth/create-password/parameters.title-token',
        'description' => 'auth/create-password/parameters.description-token',
        'type' => 'string',
        'required' => true
    ],
    'email' => [
        'field' => 'email',
        'title' => 'auth/create-password/parameters.title-email',
        'description' => 'auth/create-password/parameters.description-email',
        'type' => 'email',
        'required' => true
    ]
];
