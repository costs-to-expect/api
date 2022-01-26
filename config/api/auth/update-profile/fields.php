<?php

declare(strict_types=1);

return [
    'email' => [
        'field' => 'email',
        'title' => 'auth/update-profile/fields.title-email',
        'description' => 'auth/update-profile/fields.description-email',
        'type' => 'email',
        'required' => false
    ],
    'password' => [
        'field' => 'password',
        'title' => 'auth/update-profile/fields.title-password',
        'description' => 'auth/update-profile/fields.description-password',
        'type' => 'string',
        'validation' => [
            'min-length' => 12
        ],
        'required' => false
    ]
];
