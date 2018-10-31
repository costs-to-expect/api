<?php

namespace App\Http\Controllers;

use App\Utilities\Hash;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $hash;

    public function __construct()
    {
        $this->hash = new Hash();
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
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Header' => 'X-Requested-With, Origin, Content-Type, Accept, Authorization',
                'Access-Control-Allow-Methods' => implode(', ', array_keys($verbs)) . ', OPTIONS',
                'Content-Type' => 'application/json'
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

    /**
     * Return Validation errors
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @param array $allowed_values
     *
     * @return JsonResponse
     */
    protected function returnValidationErrors(Validator $validator, array $allowed_values = []): JsonResponse
    {
        $validation_errors = ['fields' => []];

        foreach ($validator->errors()->toArray() as $field => $errors) {
            foreach ($errors as $error) {
                $validation_errors['fields'][$field]['errors'][] = $error;
            }
        }

        if (count($allowed_values) > 0) {
            $validation_errors = array_merge_recursive($validation_errors['fields'], $allowed_values);
        }

        return response()->json(
            [
                'message' => 'Validation error',
                'fields' => $validation_errors
            ],
            422
        );
    }

    /**
     * Generate the OPTIONS request for the index routes
     *
     * @param array $get Data array to define description_key, parameters_key, conditionals and required
     * @param array $post Data array to define description_key, parameters_key, conditionals and required
     */
    protected function generateOptionsForIndex(
        array $get = [
            'description_key' => '',
            'parameters_key' => '',
            'conditionals' => [],
            'authenticated' => false
        ],
        array $post = [
            'description_key' => '',
            'fields_key' => '',
            'conditionals' => [],
            'authenticated' => true
        ]
    ) {
        $routes = [
            'GET' => [
                'description' => Config::get($get['description_key']),
                'authenticated' => $get['authenticated'],
                'parameters' => array_merge_recursive(Config::get($get['parameters_key']), $get['conditionals'])
            ],
            'POST' => [
                'description' => Config::get($post['description_key']),
                'authenticated' => $post['authenticated'],
                'fields' => array_merge_recursive(Config::get($post['fields_key']), $post['conditionals'])
            ]
        ];

        $this->optionsResponse($routes);
    }

    /**
     * Generate the OPTIONS request for the show routes
     *
     * @param string $get_description_key
     * @param string $get_parameters_key
     * @param string $delete_description_key
     */
    protected function generateOptionsForShow(
        string $get_description_key,
        string $get_parameters_key,
        string $delete_description_key
    ) {
        $routes = [
            'GET' => [
                'description' => Config::get($get_description_key),
                'authenticated' => false,
                'parameters' => Config::get($get_parameters_key)
            ],
            'DELETE' => [
                'description' => Config::get($delete_description_key),
                'authenticated' => true,
            ],
        ];

        $this->optionsResponse($routes);
    }
}
