<?php

return [
    'category' => [
        'fields' => [
            'name' => [
                'field' => 'name',
                'title' => 'Category name',
                'description' => 'Enter a name for the category',
                'type' => 'string'
            ],
            'description' => [
                'field' => 'description',
                'title' => 'Category description',
                'description' => 'Enter a description for the category',
                'type' => 'string'
            ]
        ],
        'validation' => [
            'POST' => [
                'name' => 'required|string',
                'description' => 'required|string'
            ],
            'PATCH' => [
                'name' => 'sometimes|required|string',
                'description' => 'sometimes|required|string'
            ],
        ]
    ],
    'sub_category' => [
        'fields' => [
            'name' => [
                'field' => 'name',
                'title' => 'Sub category name',
                'description' => 'Enter a name for the sub category',
                'type' => 'string'
            ],
            'description' => [
                'field' => 'description',
                'title' => 'Sub category description',
                'description' => 'Enter a description for the sub category',
                'type' => 'string'
            ]
        ],
        'validation' => [
            'POST' => [
                'name' => 'required|string',
                'description' => 'required|string'
            ],
            'PATCH' => [
                'name' => 'sometimes|required|string',
                'description' => 'sometimes|required|string'
            ],
        ]
    ],
];
