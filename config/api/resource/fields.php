<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'Resource name',
        'description' => 'Enter a name for the resource',
        'type' => 'string',
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'Resource description',
        'description' => 'Enter a description for the resource',
        'type' => 'string',
        'required' => true
    ],
    'effective_date' => [
        'field' => 'effective_date',
        'title' => 'Resource effective date',
        'description' => 'Enter an effective date for the resource',
        'type' => 'date (yyyy-mm-dd)',
        'required' => true
    ]
];
