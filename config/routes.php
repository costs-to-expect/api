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
                'fields' => [
                    'name' => 'required|string|unique:category,name',
                    'description' => 'required|string'
                ],
                'messages' => []
            ],
            'PATCH' => [
                'fields' => [
                    'name' => 'sometimes|required|string',
                    'description' => 'sometimes|required|string'
                ],
                'messages' => []
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
                'fields' => [
                    'description' => 'required|string'
                ],
                'messages' => [
                    'name.unique' => 'The sub category name has already been used within this category'
                ]
            ],
            'PATCH' => [
                'fields' => [
                    'name' => 'sometimes|required|string',
                    'description' => 'sometimes|required|string'
                ],
                'messages' => []
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
                'fields' => [
                    'name' => 'required|string|unique:resource_type,name',
                    'description' => 'required|string'
                ],
                'messages' => []
            ],
            'PATCH' => [
                'fields' => [
                    'name' => 'sometimes|required|string',
                    'description' => 'sometimes|required|string'
                ],
                'messages' => []
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            [
                'include_resources' => [
                    'field' => 'include_resources',
                    'title' => 'Include resources',
                    'description' => 'Include resources assigned to resource type',
                    'type' => 'boolean'
                ]
            ]
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
                'fields' => [
                    'description' => [
                        'required',
                        'string'
                    ],
                    'effective_date' => [
                        'required',
                        'date_format:Y-m-d'
                    ]
                ],
                'messages' => [
                    'name.unique' => 'The resource name has already been used within this resource type',
                    'effective_date.date_format' => 'The effective date does not match the required format yyyy-mm-dd (e.g. 2001-01-01)'
                ]
            ],
            'PATCH' => [
                'fields' => [
                    'name' => 'sometimes|required|string',
                    'description' => 'sometimes|required|string',
                    'effective_date' => 'sometimes|required|date_format:Y-m-d'
                ],
                'messages' => []
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
                'fields' => [
                    'description' => 'required|string',
                    'effective_date' => 'required|date_format:Y-m-d',
                    'total' => 'required|string|regex:/^\d+\.\d{2}$/',
                    'percentage' => 'required|integer|between:1,100'
                ],
                'messages' => [
                    'total.regex' => "Total cost in the format 0.00"
                ]
            ],
            'PATCH' => [
                'fields' => [
                    'description' => 'sometimes|required|string',
                    'effective_date' => 'sometimes|required|date_format:Y-m-d',
                    'total' => 'sometimes|required|string|regex:/^\d+\.\d{2}$/',
                    'percentage' => 'sometimes|required|integer|between:1,100'
                ],
                'messages' => []
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            []
        )
    ],
    'item_category' => [
        'fields' => [
            'category_id' => [
                'field' => 'category_id',
                'title' => 'Category',
                'description' => 'Which category should the item be assigned to',
                'type' => 'string'
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'category_id' => 'required|string'
                ],
                'messages' => []
            ],
            'PATCH' => [
                'fields' => [
                    'category_id' => 'required|string'
                ],
                'messages' => []
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            []
        )
    ],
    'item_sub_category' => [
        'fields' => [
            'sub_category_id' => [
                'field' => 'sub_category_id',
                'title' => 'Sub category',
                'description' => 'Which sub category should the item be assigned to',
                'type' => 'string'
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'sub_category_id' => 'required|string'
                ],
                'messages' => []
            ],
            'PATCH' => [
                'fields' => [
                    'sub_category_id' => 'required|string'
                ],
                'messages' => []
            ]
        ],
        'parameters' => array_merge(
            $pagination,
            []
        )
    ]
];
