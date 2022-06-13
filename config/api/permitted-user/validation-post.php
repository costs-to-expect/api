<?php

declare(strict_types=1);

return [
    'fields' => [
        'email' => [
            'required',
            'email'
        ]
    ],
    'messages' => [
        'email.permissible' => 'permitted-user/validation.email-permissible'
    ]
];
