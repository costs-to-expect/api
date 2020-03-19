<?php

namespace App\Http\Controllers;

use App\Models\ResourceTypeAccess;
use App\Utilities\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @var Hash
     */
    protected $hash;

    /**
     * @var boolean Include public content
     */
    protected $include_public;

    /**
     * @var array Permitted resource types
     */
    protected $permitted_resource_types = [];

    /**
     * @var integer|null
     */
    protected $user_id = null;

    /**
     * @var boolean Allow the entire collection to be returned ignoring pagination
     */
    protected $allow_entire_collection = false;

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
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Header' => 'X-Requested-With, Origin, Content-Type, Accept, Authorization',
                'Access-Control-Allow-Methods' => implode(', ', array_keys($verbs)) . ', OPTIONS',
                'Content-Type' => 'application/json',
                'Content-Language' => app()->getLocale()
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
