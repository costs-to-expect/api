<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Http\JsonResponse;

abstract class Response
{
    protected array $verbs;

    protected array $permissions;

    protected array $allowed_fields;

    protected array $allowed_parameters;

    public function __construct(array $permissions)
    {
        $this->verbs = [];

        $this->permissions = $permissions;

        $this->allowed_fields = [];
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

        response()->json(
            $options['verbs'],
            $options['http_status_code'],
            $options['headers']
        )->send();
        exit;
    }

    public function setDynamicAllowedFields(array $allowed_fields): Response
    {
        $this->allowed_fields = $allowed_fields;

        return $this;
    }

    public function setDynamicAllowedParameters(array $allowed_parameters): Response
    {
        $this->allowed_parameters = $allowed_parameters;

        return $this;
    }
}
