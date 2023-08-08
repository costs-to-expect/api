<?php

declare(strict_types=1);

return [
    'include-disabled' => [
        'parameter' => 'include-disabled',
        'title' => 'item-type-budget-pro/parameters.title-include-disabled',
        'description' => 'item-type-budget-pro/parameters.description-include-disabled',
        "default" => false,
        'type' => 'boolean',
        'required' => false
    ],
    'include-enabled' => [
        'parameter' => 'include-enabled',
        'title' => 'item-type-budget-pro/parameters.title-include-enabled',
        'description' => 'item-type-budget-pro/parameters.description-include-enabled',
        "default" => true,
        'type' => 'boolean',
        'required' => false
    ],
];
