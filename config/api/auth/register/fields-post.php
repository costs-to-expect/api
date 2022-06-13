<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'auth/register/fields.title-name',
        'description' => 'auth/register/fields.description-name',
        'type' => 'string',
        'required' => true
    ],
    'email' => [
        'field' => 'email',
        'title' => 'auth/register/fields.title-email',
        'description' => 'auth/register/fields.description-email',
        'type' => 'email',
        'required' => true
    ]
];
