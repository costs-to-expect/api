<?php

namespace App\Http\Controllers;

use App\Utilities\Hash;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var \App\Utilities\Hash
     */
    protected $hash;

    /**
     * @var bool Include private content
     */
    protected $include_private;

    public function __construct()
    {
        $this->hash = new Hash();

        $this->include_private = Auth::guard('api')->check();
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
        $validation_errors = [];

        foreach ($validator->errors()->toArray() as $field => $errors) {
            foreach ($errors as $error) {
                $validation_errors[$field]['errors'][] = $error;
            }
        }

        if (count($allowed_values) > 0) {
            $validation_errors = array_merge_recursive($validation_errors, $allowed_values);
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
     * @param array $get Data array to define description_key, parameters_key, conditionals and authenticated
     * @param array $delete Data array to define description_key and authenticated
     * @param array $patch Data array to define description_key, fields_key, conditionals and authenticated
     */
    protected function generateOptionsForShow(
        array $get = [
            'description_key' => '',
            'parameters_key' => '',
            'conditionals' => [],
            'authenticated' => false
        ],
        array $delete = [
            'description_key' => '',
            'authenticated' => false
        ],
        array $patch = [
            'description_key' => '',
            'fields_key' => '',
            'conditionals' => [],
            'authenticated' => false
        ]
    ) {
        $routes = [
            'GET' => [
                'description' => Config::get($get['description_key']),
                'authenticated' => $get['authenticated'],
                'parameters' => array_merge_recursive(Config::get($get['parameters_key']), $get['conditionals'])
            ],
            'DELETE' => [
                'description' => Config::get($delete['description_key']),
                'authenticated' => $delete['authenticated']
            ]
        ];

        if (strlen($patch['description_key']) > 0) {
            $routes['PATCH'] = [
                'description' => Config::get($patch['description_key']),
                'authenticated' => $patch['authenticated'],
                'parameters' => array_merge_recursive(Config::get($patch['fields_key']), $patch['conditionals'])
            ];
        }

        $this->optionsResponse($routes);
    }

    /**
     * Return a 400 as there is nothing to PATCH
     *
     * @return JsonResponse
     */
    protected function returnNothingToPatchError(): JsonResponse
    {
        response()->json(
            [
                'message' => 'There is nothing to PATCH, please include a request body'
            ],
            400
        )->send();
        exit();
    }

    /**
     * Return a 400 as there are invalid fields in the request body
     *
     * @param array $invalid_fields An array of invalid fields
     *
     * @return JsonResponse
     */
    protected function returnInvalidFieldsInRequestError(array $invalid_fields): JsonResponse
    {
        response()->json(
            [
                'message' => 'Non existent fields in PATCH request body',
                'fields' => $invalid_fields
            ],
            400
        )->send();
        exit();
    }

    /**
     * Check the request to ensure there is data to attempt patch
     *
     * @return boolean
     */
    protected function isThereAnythingToPatchInRequest(): bool
    {
        if (count(request()->all()) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Return success, no content (204)
     *
     * @return JsonResponse
     */
    protected function returnSuccessNoContent(): JsonResponse
    {
        response()->json([], 204)->send();
        exit();
    }

    /**
     * Check to see if there are any invalid fields in the request
     *
     * @param array $update_fields An array of fields that can be patched
     *
     * @return false|array
     */
    protected function areThereInvalidFieldsInRequest(array $update_fields)
    {
        $invalid_fields = [];
        foreach (request()->all() as $key => $value) {
            if (in_array($key, $update_fields) === false) {
                $invalid_fields[] = $key;
            }
        }

        if (count($invalid_fields) !== 0) {
            return $invalid_fields;
        }

        return false;
    }
}
