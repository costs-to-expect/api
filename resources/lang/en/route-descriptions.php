<?php

declare(strict_types=1);

return [
    'api' => [
        'GET_index' => 'Return all routes',
        'GET_changelog' => 'Return API changelog'
    ],
    'category' => [
        'GET_index' => 'Return the categories',
        'GET_show' => 'Return the requested category',
        'POST' => 'Create a new category',
        'PATCH' => 'Update the requested category',
        'DELETE' => 'Delete the requested category'
    ],
    'sub_category' => [
        'GET_index' => 'Return the sub categories',
        'GET_show' => 'Return the requested sub category',
        'POST' => 'Create a new sub category',
        'PATCH' => 'Update the requested sub category',
        'DELETE' => 'Delete the requested sub category'
    ],
    'resource_type' => [
        'GET_index' => 'Return the resource types',
        'GET_show' => 'Return the requested resource type',
        'POST' => 'Create a new resource type',
        'PATCH' => 'Update the requested resource type',
        'DELETE' => 'Delete the requested resource type'
    ],
    'resource' => [
        'GET_index' => 'Return the resources',
        'GET_show' => 'Return the requested resource',
        'POST' => 'Create a new resource',
        'PATCH' => 'Update the requested resource',
        'DELETE' => 'Delete the requested resource'
    ],
    'item' => [
        'GET_index' => 'Return the items',
        'GET_show' => 'Return the requested item',
        'POST' => 'Create a new item',
        'PATCH' => 'Update the requested item',
        'DELETE' => 'Delete the requested item'
    ],
    'item_category' => [
        'GET_index' => 'Return the category the item is assigned to',
        'GET_show' => 'Return the category the item is assigned to',
        'POST' => 'Assign the category',
        'PATCH' => 'Update the category',
        'DELETE' => 'Remove the assigned category'
    ],
    'item_sub_category' => [
        'GET_index' => 'Return the sub category the item is assigned to',
        'GET_show' => 'Return the sub category the item is assigned to',
        'POST' => 'Assign the sub category',
        'PATCH' => 'Update the sub category',
        'DELETE' => 'Remove the assigned sub category'
    ],
    'summary' => [
        'GET_tco' => 'Total cost of ownership TCO for a resource',
        'GET_categories' => 'Return the categories summary for a resource',
        'GET_expanded_categories' => 'Return the expanded categories summary for a resource',
        'GET_category' => 'Return the category summary for a resource',
        'GET_sub_categories' => 'Return the category sub categories summary for a resource',
        'GET_sub_category' => 'Return the category sub category summary for a resource',
        'GET_years' => 'Return the years summary for a resource',
        'GET_year' => 'Return the year summary for a resource',
        'GET_months' => 'Return the months summary for a resource and year',
        'GET_month' => 'Return the month summary for a resource and year',
    ],
    'request' => [
        'GET_log' => 'Return the request log',
        'GET_log_monthly_requests' => 'Return the number of logged requests per month',
        'GET_error_log' => 'Return the request error log',
        'POST' => 'Log an API request error'
    ]
];
