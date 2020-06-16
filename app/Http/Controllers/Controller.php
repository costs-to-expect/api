<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use App\Models\ResourceTypeAccess;
use App\Utilities\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected Hash $hash;

    protected bool $include_public;

    protected array $permitted_resource_types = [];

    protected array $public_resource_types = [];

    /**
     * @var integer|null
     */
    protected ?int $user_id = null;

    /**
     * @var bool Allow the entire collection to be returned ignoring pagination
     */
    protected bool $allow_entire_collection = false;

    public function __construct()
    {
        $this->hash = new Hash();

        $this->middleware(function ($request, $next) {
            $this->setHelperProperties();

            return $next($request);
        });
    }

    protected function setHelperProperties()
    {
        if (auth()->guard('api')->check() === true && auth('api')->user() !== null) {
            $this->user_id = auth('api')->user()->id;
            $this->permitted_resource_types = (new ResourceTypeAccess())->permittedResourceTypes($this->user_id);
            $this->public_resource_types = (new ResourceType())->publicResourceTypes();
        }

        $this->include_public = true;
    }

    /**
     * Generate and return the options response
     *
     * @param array $verbs Verb arrays
     * @param integer $http_status_code, defaults to 200
     *
     * @return JsonResponse
     */
    protected function optionsResponse(array $verbs, $http_status_code = 200): JsonResponse
    {
        $options = [
            'verbs' => [],
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

        foreach ($verbs as $verb => $detail) {
            $options['verbs'][$verb] = $detail;
        }

        response()->json(
            $options['verbs'],
            $options['http_status_code'],
            $options['headers']
        )->send();
        exit;
    }
}
