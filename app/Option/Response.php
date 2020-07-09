<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Http\JsonResponse;

abstract class Response
{
    protected array $verbs;

    protected array $permissions;

    public function __construct(array $permissions)
    {
        $this->verbs = [];

        $this->permissions = $permissions;
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
}
