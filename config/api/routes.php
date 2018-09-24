<?php

$pagination = [
    'offset' => [
        'parameter' => 'offset',
        'title' => 'Record offset for pagination',
        'default' => 0,
        'type' => 'integer',
        'required' => false
    ],
    'limit' => [
        'parameter' => 'limit',
        'title' => 'Record limit for pagination',
        'default' => 10,
        'type' => 'integer',
        'required' => false
    ]
];

return [
    'category' => [
        'fields' => [
            'name' => [
                'field' => 'name',
                'title' => 'Category name',
                'description' => 'Enter a name for the category',
                'type' => 'string',
                'required' => true
            ],
            'description' => [
                'field' => 'description',
                'title' => 'Category description',
                'description' => 'Enter a description for the category',
                'type' => 'string',
                'required' => true
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'name' => 'required|string|unique:category,name',
                    'description' => 'required|string'
                ],
                'messages' => [
                    'name.unique' => 'The category name has already been used'
                ]
            ],
            'PATCH' => [
                'fields' => [
                    'description' => 'sometimes|required|string'
                ],
                'messages' => [
                    'name.unique' => 'The category name has already been used'
                ]
            ]
        ],
        'parameters' => [
            'collection' => [
                'include_sub_categories' => [
                    'field' => 'include_sub_categories',
                    'title' => 'Include sub categories',
                    'description' => 'Include sub categories assigned to this category',
                    'type' => 'boolean',
                    'required' => false
                ]
            ],
            'item' => [
                'include_sub_categories' => [
                    'field' => 'include_sub_categories',
                    'title' => 'Include sub categories',
                    'description' => 'Include sub categories assigned to this category',
                    'type' => 'boolean',
                    'required' => false
                ]
            ]
        ]
    ],
    'sub_category' => [
        'fields' => [
            'name' => [
                'field' => 'name',
                'title' => 'Sub category name',
                'description' => 'Enter a name for the sub category',
                'type' => 'string',
                'required' => true
            ],
            'description' => [
                'field' => 'description',
                'title' => 'Sub category description',
                'description' => 'Enter a description for the sub category',
                'type' => 'string',
                'required' => true
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
                'messages' => [
                    'name.unique' => 'The sub category name has already been used within this category'
                ]
            ]
        ],
        'parameters' => [
            'collection' => [],
            'item' => []
        ]
    ],
    'resource_type' => [
        'fields' => [
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
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'name' => 'required|string|unique:resource_type,name',
                    'description' => 'required|string'
                ],
                'messages' => [
                    'name.unique' => 'The resource type has already been used'
                ]
            ],
            'PATCH' => [
                'fields' => [
                    'name' => 'sometimes|required|string',
                    'description' => 'sometimes|required|string'
                ],
                'messages' => [
                    'name.unique' => 'The resource type has already been used'
                ]
            ]
        ],
        'parameters' => [
            'collection' => [
                'include_resources' => [
                    'field' => 'include_resources',
                    'title' => 'Include resources',
                    'description' => 'Include resources assigned to resource type',
                    'type' => 'boolean',
                    'required' => false
                ]
            ],
            'item' => [
                'include_resources' => [
                    'field' => 'include_resources',
                    'title' => 'Include resources',
                    'description' => 'Include resources assigned to resource type',
                    'type' => 'boolean',
                    'required' => false
                ]
            ]
        ]
    ],
    'resource' => [
        'fields' => [
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
                'messages' => [
                    'name.unique' => 'The resource name has already been used within this resource type'
                ]
            ]
        ],
        'parameters' => [
            'collection' => [],
            'item' => []
        ]
    ],
    'item' => [
        'fields' => [
            'description' => [
                'field' => 'description',
                'title' => 'Item description',
                'description' => 'Enter a description for the item',
                'type' => 'string',
                'required' => true
            ],
            'effective_date' => [
                'field' => 'effective_date',
                'title' => 'Item effective date',
                'description' => 'Enter the effective date for the item',
                'type' => 'date (yyyy-mm-dd)',
                'required' => true
            ],
            'total' => [
                'field' => 'total',
                'title' => 'Resource total',
                'description' => 'Enter the total amount for the item',
                'type' => 'decimal (10,2)',
                'required' => true
            ],
            'percentage' => [
                'field' => 'percentage',
                'title' => 'Resource effective date',
                'description' => 'Enter the percentage to allot, defaults to 100 if not supplied',
                'type' => 'string',
                'required' => false
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'description' => 'required|string',
                    'effective_date' => 'required|date_format:Y-m-d',
                    'total' => 'required|string|regex:/^\d+\.\d{2}$/',
                    'percentage' => 'sometimes|required|integer|between:1,100'
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
        'parameters' => [
            'collection' => array_merge(
                $pagination,
                [
                    'year' => [
                        "parameter" => "year",
                        "title" => "Show results for given year",
                        "default" => null,
                        "type" => "integer",
                        "required" => false
                    ],
                    'month' => [
                        "parameter" => "month",
                        "title" => "Show results for given month",
                        "default" => null,
                        "type" => "integer",
                        "required" => false
                    ],
                    'category' => [
                        "parameter" => "category",
                        "title" => "Show results for selected category",
                        "default" => null,
                        "type" => "string",
                        "required" => false
                    ],
                    'sub_category' => [
                        "parameter" => "sub_category",
                        "title" => "Show results for selected sub category (Only relevant if category set)",
                        "default" => null,
                        "type" => "string",
                        "required" => false
                    ]
                ]
            ),
            'item' => []
        ]
    ],
    'item_category' => [
        'fields' => [
            'category_id' => [
                'field' => 'category_id',
                'title' => 'Category',
                'description' => 'Which category should the item be assigned to',
                'type' => 'string',
                'required' => true
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [
                    'category_id' => 'required|exists:category,id'
                ],
                'messages' => [
                    'category_id.required' => 'Category field required or could not be decoded',
                    'category_id.exists' => "Given category id does not exist"
                ]
            ],
            'PATCH' => [
                'fields' => [
                    'category_id' => 'required|string'
                ],
                'messages' => []
            ]
        ],
        'parameters' => [
            'collection' => [],
            'item' => []
        ]
    ],
    'item_sub_category' => [
        'fields' => [
            'sub_category_id' => [
                'field' => 'sub_category_id',
                'title' => 'Sub category',
                'description' => 'Which sub category should the item be assigned to',
                'type' => 'string',
                'required' => true
            ]
        ],
        'validation' => [
            'POST' => [
                'fields' => [

                ],
                'messages' => [
                    'sub_category_id.required' => 'Sub category field required or could not be decoded',
                    'sub_category_id.exists' => "Given sub category id does not exist"
                ]
            ],
            'PATCH' => [
                'fields' => [
                    'sub_category_id' => 'required|string'
                ],
                'messages' => []
            ]
        ],
        'parameters' => [
            'collection' => [],
            'item' => []
        ]
    ]
];
