<?php

declare(strict_types=1);

return [
    'user_id' => [
        'field' => 'user_id',
        'title' => 'permitted-user/fields.title-user_id',
        'description' => 'permitted-user/fields.description-user_id',
        'type' => 'string',
        'validation' => [
            'length' => 10
        ],
        'required' => true
    ]
];
