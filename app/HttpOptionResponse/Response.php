<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Http\JsonResponse;

abstract class Response
{
    protected array $verbs;

    protected array $permissions;

    protected array $allowed_values_for_fields;

    protected array $allowed_values_for_parameters;

    public function __construct(array $permissions)
    {
        $this->verbs = [];

        $this->permissions = $permissions;

        $this->allowed_values_for_fields = [];
    }

    abstract public function create();

    public function response(int $http_status_code = 200): JsonResponse
    {
        $options = [
            'verbs' => $this->verbs,
            'http_status_code' => $http_status_code,
            'headers' => [
                'Content-Security-Policy' => 'default-src \'none\'',
                'Strict-Transport-Security' => 'max-age=31536000;',
                'Content-Type' => 'application/json',
                'Content-Language' => app()->getLocale(),
                'Referrer-Policy' => 'strict-origin-when-cross-origin',
                'X-Content-Type-Options' => 'nosniff'
            ]
        ];

        return response()->json(
            $options['verbs'],
            $options['http_status_code'],
            $options['headers']
        );
    }

    public function setAllowedValuesForFields(array $allowed_fields): Response
    {
        $this->allowed_values_for_fields = $allowed_fields;

        return $this;
    }

    public function setAllowedValuesForParameters(array $allowed_parameters): Response
    {
        $this->allowed_values_for_parameters = $allowed_parameters;

        return $this;
    }
}
