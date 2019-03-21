<?php

return [
    'api_GET_index' => 'Return all routes',
    'api_GET_changelog' => 'Return API changelog',

    'category_GET_index' => 'Return the categories',
    'category_GET_show' => 'Return the requested category',
    'category_POST' => 'Create a new category',
    'category_PATCH' => 'Update the requested category',
    'category_DELETE' => 'Delete the requested category',

    'sub_category_GET_index' => 'Return the sub categories',
    'sub_category_GET_show' => 'Return the requested sub category',
    'sub_category_POST' => 'Create a new sub category',
    'sub_category_PATCH' => 'Update the requested sub category',
    'sub_category_DELETE' => 'Delete the requested sub category',

    'resource_type_GET_index' => 'Return the resource types',
    'resource_type_GET_show' => 'Return the requested resource type',
    'resource_type_POST' => 'Create a new resource type',
    'resource_type_PATCH' => 'Update the requested resource type',
    'resource_type_DELETE' => 'Delete the requested resource type',

    'resource_GET_index' => 'Return the resources',
    'resource_GET_show' => 'Return the requested resource',
    'resource_POST' => 'Create a new resource',
    'resource_PATCH' => 'Update the requested resource',
    'resource_DELETE' => 'Delete the requested resource',

    'item_GET_index' => 'Return the items',
    'item_GET_show' => 'Return the requested item',
    'item_POST' => 'Create a new item',
    'item_PATCH' => 'Update the requested item',
    'item_DELETE' => 'Delete the requested item',

    'item_category_GET_index' => 'Return the category the item is assigned to',
    'item_category_GET_show' => 'Return the category the item is assigned to',
    'item_category_POST' => 'Assign the category',
    'item_category_PATCH' => 'Update the category',
    'item_category_DELETE' => 'Remove the assigned category',

    'item_sub_category_GET_index' => 'Return the sub category the item is assigned to',
    'item_sub_category_GET_show' => 'Return the sub category the item is assigned to',
    'item_sub_category_POST' => 'Assign the sub category',
    'item_sub_category_PATCH' => 'Update the sub category',
    'item_sub_category_DELETE' => 'Remove the assigned sub category',

    'summary_GET_tco' => 'Total cost of ownership TCO for a resource',
    'summary_GET_categories' => 'Return the categories summary for a resource',
    'summary_GET_expanded_categories' => 'Return the expanded categories summary for a resource',
    'summary_GET_category' => 'Return the category summary for a resource',
    'summary_GET_sub_categories' => 'Return the category sub categories summary for a resource',
    'summary_GET_sub_category' => 'Return the category sub category summary for a resource',
    'summary_GET_years' => 'Return the years summary for a resource',
    'summary_GET_year' => 'Return the year summary for a resource',
    'summary_GET_months' => 'Return the months summary for a resource and year',
    'summary_GET_month' => 'Return the month summary for a resource and year',

    'request_GET_log' => 'Return the request log',
    'request_GET_log_monthly_requests' => 'Return the number of logged requests per month',
    'request_GET_error_log' => 'Return the request error log',
    'request_POST' => 'Log an API request error',
];
