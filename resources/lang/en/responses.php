<?php

declare(strict_types=1);

return [
    'not-found' => 'The requested resource does not exist.',
    'not-found-entity' => 'The requested `:type` does not exist.',
    'not-found-or-not-accessible-entity' => 'The requested `:type` does not exist or is not accessible with your permissions.',
    'constraint' => 'Unable to handle your request, dependent data exists.',
    'model-select-failure' => 'Unable to handle your request, an error occurred when selecting the data to complete your request.',
    'model-save-failure-update' => 'Unable to handle your request, an error occurred when processing your update request.',
    'model-save-failure-create' => 'Unable to handle your request, an error occurred when processing your create request.',
    'decode-error' => 'Unable to decode a parameter, a suitable hash class not found or the value is invalid.',
    'patch-empty' => 'Unable to handle your request, please include a request body.',
    'patch-invalid' => 'Unable to handle your request, there are non-existent fields in your request body.',
    'validation' => 'Validation error.',
    'authentication-required' => 'Authentication required, please try again with a Bearer.',
    'maintenance' => 'Down for maintenance, we should be back very soon, please check https://status.costs-to-expect.com for more information',
    'error' => 'Sorry, there has been an error, please try again later.'
];
