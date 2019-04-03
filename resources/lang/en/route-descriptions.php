<?php

declare(strict_types=1);

return [
    'api_GET_index' => 'Return all the API routes',
    'api_GET_changelog' => 'Return the complete API changelog',

    'category_GET_index' => 'Return all the public categories, optionally, with authorisation include any private categories',
    'category_GET_show' => 'Return the selected category',
    'category_POST' => 'Create a new category',
    'category_PATCH' => 'Update the selected category',
    'category_DELETE' => 'Delete the selected category',

    'sub_category_GET_index' => 'Return all the subcategories that are children of the selected category',
    'sub_category_GET_show' => 'Return the selected subcategory',
    'sub_category_POST' => 'Create a new subcategory',
    'sub_category_PATCH' => 'Update the selected subcategory',
    'sub_category_DELETE' => 'Delete the selected subcategory',

    'resource_type_GET_index' => 'Return all the public resource types, optionally, with authorisation include any private resource types',
    'resource_type_GET_show' => 'Return the selected resource type',
    'resource_type_POST' => 'Create a new resource type',
    'resource_type_PATCH' => 'Update the selected resource type',
    'resource_type_DELETE' => 'Delete the selected resource type',

    'resource_GET_index' => 'Return all the resources that are children of the selected resource type',
    'resource_GET_show' => 'Return the selected resource',
    'resource_POST' => 'Create a new resource',
    'resource_PATCH' => 'Update the selected resource',
    'resource_DELETE' => 'Delete the selected resource',

    'item_GET_index' => 'Return all the items that are children of the selected resource',
    'item_GET_show' => 'Return the selected item',
    'item_POST' => 'Create a new item',
    'item_PATCH' => 'Update the selected item',
    'item_DELETE' => 'Delete the selected item',

    'item_category_GET_index' => 'Return the category assigned to the selected item',
    'item_category_GET_show' => 'Return the category assigned to the selected item',
    'item_category_POST' => 'Assign a category to the selected item',
    'item_category_PATCH' => 'Update the category assigned to the selected item',
    'item_category_DELETE' => 'Delete the category assigned to the selected item',

    'item_sub_category_GET_index' => 'Return the subcategory assigned to the selected item',
    'item_sub_category_GET_show' => 'Return the subcategory assigned to the selected item',
    'item_sub_category_POST' => 'Assign a subcategory to the selected item',
    'item_sub_category_PATCH' => 'Update the subcategory assigned to the selected item',
    'item_sub_category_DELETE' => 'Delete the subcategory assigned to the selected item',

    'summary_GET_tco' => 'Return the total costs of ownership (sum of items) for the selected resource',
    'summary_GET_categories' => 'Return the categories summary (sum of items) for the selected resource',
    'summary_GET_expanded_categories' => 'Return the categories summary (sum of items) for the selected resource, includes categories with no data',
    'summary_GET_category' => 'Return the category summary (sum of items) for the selected resource and category',
    'summary_GET_sub_categories' => 'Return the subcategories summary (sum of items) for the selected resource and category',
    'summary_GET_sub_category' => 'Return the subcategory summary (sum of items) for the selected resource, category and subcategory',
    'summary_GET_years' => 'Return the annualised summary (sum of items) for the selected resource',
    'summary_GET_year' => 'Return the annualised summary (sum of items) for the selected resource and year',
    'summary_GET_months' => 'Return the monthly summary (sum of items) for the selected resource and year',
    'summary_GET_month' => 'Return the monthly summary (sum of items) for the selected resource, year and month',

    'request_GET_access-log' => 'Return the access log, read requests',
    'request_GET_error_log' => 'Return the error log',
    'request_POST' => 'Create an error log report',

    'summary_GET_request_access-log_monthly' => 'Return a summary of the access log, read requests, grouped by month'
];
