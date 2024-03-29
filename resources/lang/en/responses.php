<?php

declare(strict_types=1);

return [
    'not-found' => 'The requested resource does not exist.',
    'not-found-entity' => 'The requested `:type` does not exist.',
    'not-found-or-not-accessible-entity' => 'The requested `:type` does not exist or is not accessible with your permissions.',
    'not-supported' => 'The requested route is not supported for the item type',
    'constraint' => 'Unable to handle your request, dependent data exists or foreign key error.',
    'constraint-category' => 'Unable to handle your request, category data exists for the item, delete the category first.',
    'model-select-failure' => 'Unable to handle your request, an error occurred when selecting the data to complete your request.',
    'model-save-failure-update' => 'Unable to handle your request, an error occurred when processing your update request.',
    'model-save-failure-create' => 'Unable to handle your request, an error occurred when processing your create request.',
    'decode-error' => 'Unable to decode a parameter, a suitable hash class not found or the value is invalid.',
    'patch-empty' => 'Unable to handle your request, please include a request body.',
    'patch-invalid' => 'Unable to handle your request, there are non-patchable or non-existent fields in the request body, please only include the fields you want to patch.',
    'validation' => 'Validation error.',
    'authentication-required' => 'Authentication required, please try again with a Bearer.',
    'maintenance' => 'Down for maintenance, we should be back very soon, please check https://status.costs-to-expect.com for more information',
    'error' => 'Sorry, there has been an error, please try again later.',
    'category-limit' => 'Unable to handle your request, the number of allowable category assignments reached',
    'subcategory-limit' => 'Unable to handle your request, the number of allowable subcategory assignments reached',
    'invalid-route' => 'The requested route is invalid, please visit the index of the API to see all the valid routes https://api.costs-to-expect.com/v3',
    'delete-requested' => 'The delete request has been received, a job has been added to the queue to process the request',
    'migrate-requested' => 'The migration request has been received, a job has been added to the queue to process the request'
];
