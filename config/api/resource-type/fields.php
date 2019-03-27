<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'Resource type name',
        'description' => 'Enter a name for the resource type',
        'type' => 'string',
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'Resource type description',
        'description' => 'Enter a description for the resource type',
        'type' => 'string',
        'required' => true
    ],
    'private' => [
        'field' => 'private',
        'title' => 'Is the a private resource type',
        'description' => 'Please set whether this should be marked as a private resource type',
        'type' => 'boolean',
        'required' => true
    ]
];
