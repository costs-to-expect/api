<?php

declare(strict_types=1);

return [
    'password' => [
        'field' => 'password',
        'title' => 'auth/create-new-password/fields.title-password',
        'description' => 'auth/create-new-password/fields.description-password',
        'type' => 'string',
        'validation' => [
            'min-length' => 12
        ],
        'required' => true
    ],
    'password_confirmation' => [
        'field' => 'description',
        'title' => 'category/fields.title-description',
        'description' => 'category/fields.description-description',
        'type' => 'string',
        'validation' => [
            'matches' => 'password',
            'min-length' => 12
        ],
        'required' => true
    ]
];
