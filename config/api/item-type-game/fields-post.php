<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'item-type-game/fields.title-name',
        'description' => 'item-type-game/fields.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-game/fields.title-description',
        'description' => 'item-type-game/fields.description-description',
        'type' => 'string',
        'required' => false
    ]
];
