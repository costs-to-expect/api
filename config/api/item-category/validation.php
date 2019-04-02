<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'category_id' => 'required|exists:category,id'
        ],
        'messages' => [
            'category_id.required' => 'item-category/validation.category_id-required',
            'category_id.exists' => 'item-category/validation.category_id-exists'
        ]
    ]
];
