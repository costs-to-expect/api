<?php

declare(strict_types=1);

return [
    'auth_create_new_password_POST' => 'Create a new password for your account, `token` and `email` are required parameters and validate the request',
    'auth_create_password_POST' => 'Create the password for your account, `token` and `email` are required parameters and validate the request',
    'auth_forgot_password_POST' => 'Start the reset password process',
    'auth_login_POST' => 'Login to the API',
    'auth_register_POST' => 'Register with the API',
    'auth_update_password_POST' => 'Update your account password',
    'auth_update_profile_POST' => 'Update your account profile',
    'auth_user_GET' => 'Return the account details for the signed-in user',
    'auth_user_tokens_GET' => 'Return the tokens for the signed-in user',
    'auth_user_token_GET' => 'Return the requested token for the signed-in user',
    'auth_user_token_DELETE' => 'Delete the requested token for the signed-in user',
    'auth_check_GET' => 'Check to see if the user is authenticated',

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

    'item_type_GET_index' => 'Return all the item types supported in the API',
    'item_type_GET_show' => 'Return the selected item type',

    'item_subtype_GET_index' => 'Return all the item subtypes supported by the selected item type',
    'item_subtype_GET_show' => 'Return the selected item subtype',

    'currency_GET_index' => 'Return all the currencies supported in the API',
    'currency_GET_show' => 'Return the selected currency',

    'queue_GET_index' => 'Return all the jobs in the queue, delayed by five minutes',
    'queue_GET_show' => 'Return the selected queue job',

    'resource_type_GET_index' => 'Return all the public resource types, optionally, with authorisation include any private resource types',
    'resource_type_GET_show' => 'Return the selected resource type',
    'resource_type_POST' => 'Create a new resource type',
    'resource_type_PATCH' => 'Update the selected resource type',
    'resource_type_DELETE' => 'Delete the selected resource type',

    'resource_type_item_GET_index' => 'Return all the items assigned to the resources for this resource type',

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

    'item_category_POST_allocated_expense' => 'Assign a maximum of one category to the selected allocated-expense',
    'item_category_POST_game' => 'Assign the categories (players) to the selected game',

    'item_category_PATCH' => 'Update the category assigned to the selected item',
    'item_category_DELETE' => 'Delete the category assigned to the selected item',

    'item_sub_category_GET_index' => 'Return the subcategory assigned to the selected item',
    'item_sub_category_GET_show' => 'Return the subcategory assigned to the selected item',

    'item_sub_category_POST_allocated_expense' => 'Assign a maximum of one subcategory to the selected allocated-expense',

    'item_sub_category_PATCH' => 'Update the subcategory assigned to the selected item',
    'item_sub_category_DELETE' => 'Delete the subcategory assigned to the selected item',

    'item_transfer_GET_index' => 'Return the transfers for the selected resource type',
    'item_transfer_GET_show' => 'Return the selected transfer',
    'item_transfer_POST' => 'Transfer an item to another resource',

    'item_partial_transfer_GET_index' => 'Return the partial transfers for the selected resource type',
    'item_partial_transfer_GET_show' => 'Return the selected partial transfer',
    'item_partial_transfer_POST' => 'Reassign a percentage of the total for an item to another resource',
    'item_partial_transfer_DELETE' => 'Delete the selected partial transfer',

    'item_data_GET_index' => 'Return the keyed data collection',
    'item_data_POST' => 'Send keyed data',

    'permitted_user_GET_index' => 'Return the permitted users',
    'permitted_user_GET_show' => 'Return the selected permitted user',
    'permitted_user_POST' => 'Assign a permitted user',
    'permitted_user_DELETE' => 'Delete the selected permitted user',

    'request_GET_error_log' => 'Return the error log',
    'request_GET_cache' => 'Return the number of cached keys for the authenticated user',
    'request_DELETE_cache' => 'Attempt to delete the cached keys for the authenticated user',
    'request_POST' => 'Create an error log report',

    'summary_category_GET_index' => 'Return a summary of the categories',
    'summary_subcategory_GET_index' => 'Return a summary of the subcategories',

    'summary_resource_type_GET_index' => 'Return a summary of the resource types',
    'summary_resource_GET_index' => 'Return a summary of the resources',

    'summary_items_GET_index'=> 'Return the "item" summary for the selected resource, review summary filters for all summary options',
    'summary_resource_type_items_GET_index'=> 'Return the "item" summary for the selected resource type, items for all resources are summarised, review summary filters for all summary options',
];
