<?php

namespace App\Http\Controllers;

use Hashids\Hashids;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $hash;

    public function __construct()
    {
        $this->hash = new Hashids('costs-to-expect', 10);
    }

    /**
     * Generate the options response for an endpoint
     *
     * @param array $verbs Verb arrays
     * @param integer $http_status_code, defaults to 200
     *
     * @return array Three indexes, verbs, status and headers
     */
    protected function generateOptionsResponse(array $verbs, $http_status_code = 200)
    {
        $options = [
            'verbs' => [],
            'http_status_code' => $http_status_code,
            'headers' => [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Header' => 'X-Requested-With, Origin, Content-Type, Accept, Authorization',
                'Access-Control-Allow-Methods' => implode(', ', array_keys($verbs)) . ', OPTIONS',
                'Content-Type' => 'application/json'
            ]
        ];

        foreach ($verbs as $verb => $detail) {
            $options['verbs'][$verb] = $detail;
        }

        return $options;
    }

    /**
     * Return bad request as there are no fields to patch
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function requireAtLeastOneFieldToPatch()
    {
        return response()->json(
            [
                'error' => 'Bad request, you need to supply at least one field to patch'
            ],
            400
        );
    }

    /**
     * Return Validation errors
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnValidationErrors(Validator $validator)
    {
        return response()->json(
            [
                'error' => 'Validation error',
                'fields' => $validator->errors()
            ],
            422
        );
    }

    protected function generateOptionsForIndex(
        $get_description_key,
        $post_description_key,
        $post_fields_key
    )
    {
        $routes = [
            'GET' => [
                'description' => Config::get($get_description_key),
                'authenticated' => false,
                'parameters' => []
            ],
            'POST' => [
                'description' => Config::get($post_description_key),
                'authenticated' => true,
                'fields' => Config::get($post_fields_key)
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    protected function generateOptionsForShow(
        $get_description_key,
        $delete_description_key,
        $patch_description_key,
        $patch_fields_key
    )
    {
        $routes = [
            'GET' => [
                'description' => Config::get($get_description_key),
                'authenticated' => false,
                'parameters' => []
            ],
            'DELETE' => [
                'description' => Config::get($delete_description_key),
                'authenticated' => true,
            ],
            'PATCH' => [
                'description' => Config::get($patch_description_key),
                'authenticated' => true,
                'fields' => Config::get($patch_fields_key)
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }
}
