<?php

declare(strict_types=1);

return [
    'POST' => [
        'fields' => [
            'subcategory_id' => [
                'required',
                'exists:sub_category,id'
            ]
        ],
        'messages' => [
            'subcategory_id.required' => 'item-subcategory/validation.subcategory_id-required',
            'subcategory_id.exists' => 'item-subcategory/validation.subcategory_id-required'
        ]
    ]
];
