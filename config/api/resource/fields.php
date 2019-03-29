<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'resource/fields.title-name',
        'description' => 'resource/fields.description-name',
        'type' => 'string',
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'resource/fields.title-description',
        'description' => 'resource/fields.description-description',
        'type' => 'string',
        'required' => true
    ],
    'effective_date' => [
        'field' => 'effective_date',
        'title' => 'resource/fields.title-effective_date',
        'description' => 'resource/fields.description-effective_date',
        'type' => 'date (yyyy-mm-dd)',
        'required' => true
    ]
];
