<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'sub_category_id' => 'required|exists:sub_category,id'
        ],
        'messages' => [
            'sub_category_id.required' => 'item-subcategory/validation.sub_category_id-required',
            'sub_category_id.exists' => 'item-subcategory/validation.sub_category_id-required'
        ]
    ]
];
