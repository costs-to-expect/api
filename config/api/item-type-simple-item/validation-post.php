<?php

declare(strict_types=1);

return [
    'fields' => [
        'name' =>
            'required',
        'string',
        'max:255'
    ],
    'description' => [
        'sometimes',
        'string'
    ],
    'quantity' => [
        'required',
        'integer',
        'min:0'
    ],
    'messages' => []
];
