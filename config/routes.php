<?php

$pagination = [
    'offset' => [
        'parameter' => 'offset',
        'title' => 'Record offset for pagination',
        'default' => 0,
        'type' => 'integer'
    ],
    'limit' => [
        'parameter' => 'limit',
        'title' => 'Record limit for pagination',
        'default' => 10,
        'type' => 'integer'
    ]
];

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
                'name' => 'required|string|unique:category,name',
                'description' => 'required|string'
            ],
            'PATCH' => [
                'name' => 'sometimes|required|string',
                'description' => 'sometimes|required|string'
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            []
        )
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
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            []
        )
    ],
    'resource_type' => [
        'fields' => [
            'name' => [
                'field' => 'name',
                'title' => 'Resource type name',
                'description' => 'Enter a name for the resource type',
                'type' => 'string'
            ],
            'description' => [
                'field' => 'description',
                'title' => 'Resource type description',
                'description' => 'Enter a description for the resource type',
                'type' => 'string'
            ]
        ],
        'validation' => [
            'POST' => [
                'name' => 'required|string|unique:resource_type,name',
                'description' => 'required|string'
            ],
            'PATCH' => [
                'name' => 'sometimes|required|string',
                'description' => 'sometimes|required|string'
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            []
        )
    ],
    'resource' => [
        'fields' => [
            'name' => [
                'field' => 'name',
                'title' => 'Resource name',
                'description' => 'Enter a name for the resource',
                'type' => 'string'
            ],
            'description' => [
                'field' => 'description',
                'title' => 'Resource description',
                'description' => 'Enter a description for the resource',
                'type' => 'string'
            ],
            'effective_date' => [
                'field' => 'effective_date',
                'title' => 'Resource effective date',
                'description' => 'Enter an effective date for the resource',
                'type' => 'date (yyyy-mm-dd)'
            ]
        ],
        'validation' => [
            'POST' => [
                'description' => [
                    'required',
                    'string'
                ],
                'effective_date' => [
                    'required',
                    'date_format:Y-m-d'
                ]
            ],
            'PATCH' => [
                'name' => 'sometimes|required|string',
                'description' => 'sometimes|required|string',
                'effective_date' => 'sometimes|required|date_format:Y-m-d'
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            []
        )
    ],
    'item' => [
        'fields' => [
            'description' => [
                'field' => 'description',
                'title' => 'Item description',
                'description' => 'Enter a description for the item',
                'type' => 'string'
            ],
            'effective_date' => [
                'field' => 'effective_date',
                'title' => 'Item effective date',
                'description' => 'Enter the effective date for the item',
                'type' => 'date (yyyy-mm-dd)'
            ],
            'total' => [
                'field' => 'total',
                'title' => 'Resource total',
                'description' => 'Enter the total amount for the item',
                'type' => 'decimal (10,2)'
            ],
            'percentage' => [
                'field' => 'percentage',
                'title' => 'Resource effective date',
                'description' => 'Enter the percentage to allot, defaults to 100',
                'type' => 'string'
            ]
        ],
        'validation' => [
            'POST' => [
                'description' => 'required|string',
                'effective_date' => 'required|date_format:Y-m-d',
                'total' => 'required|regex:/^\d+\.\d{2}$/',
                'percentage' => 'required|integer|between:1,100'
            ],
            'PATCH' => [
                'description' => 'sometimes|required|string',
                'effective_date' => 'sometimes|required|date_format:Y-m-d',
                'total' => 'sometimes|required|regex:/^\d+\.\d{2}$/',
                'percentage' => 'sometimes|required|integer|between:1,100'
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            []
        )
    ]
];
