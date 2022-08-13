<?php

declare(strict_types=1);

return [
    'message' => [
        'field' => 'message',
        'title' => 'item-log/fields-post.title-message',
        'description' => 'item-log/fields-post.description-message',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'parameters' => [
        'field' => 'parameters',
        'title' => 'item-log/fields-post.title-parameters',
        'description' => 'item-log/fields-post.description-parameters',
        'type' => 'json',
        'required' => true
    ]
];
