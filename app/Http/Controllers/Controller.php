<?php

namespace App\Http\Controllers;

use App\Utilities\Hash;
use App\Utilities\Response as UtilityResponse;
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
     */
    protected function returnValidationErrors(Validator $validator, array $allowed_values = [])
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

        UtilityResponse::validationErrors($validation_errors);
    }

    /**
     * Generate the OPTIONS request for the index (collection) routes
     *
     * @param array $get Four indexes, description_localisation, parameters_config, conditionals, authenticated and pagination
     * @param array $post Four indexes, description_localisation, fields_config, conditionals and authenticated
     */
    protected function generateOptionsForIndex(
        array $get = [
            'description_localisation_string' => '',
            'parameters_config_string' => null,
            'conditionals_config' => [],
            'sortable_config' => null,
            'searchable_config' => null,
            'enable_pagination' => true,
            'authentication_required' => false
        ],
        array $post = [
            'description_localisation_string' => '',
            'fields_config' => '',
            'conditionals_config' => [],
            'authentication_required' => true
        ]
    ) {
        $get_parameters = [];
        $get_parameters_sortable = false;
        $get_parameters_searchable = false;
        $post_fields = [];

        if ($get['parameters_config_string'] !== null) {
            foreach (
                array_merge_recursive(
                    ($get['enable_pagination'] === true ? Config::get('api.pagination.parameters') : []),
                    ($get['sortable_config'] !== null ? Config::get('api.sortable.parameters') : []),
                    ($get['searchable_config'] !== null ? Config::get('api.searchable.parameters') : []),
                    Config::get($get['parameters_config_string']),
                    $get['conditionals_config']
                ) as $parameter => $detail) {
                $detail['title'] = trans($detail['title']);
                $detail['description'] = trans($detail['description']);
                $get_parameters[$parameter] = $detail;
            }
        }

        if ($get['sortable_config'] !== null) {
            $get_parameters_sortable = Config::get($get['sortable_config']);
        }

        if ($get['searchable_config'] !== null) {
            $get_parameters_searchable = Config::get($get['searchable_config']);
        }

        $routes = [
            'GET' => [
                'description' => trans($get['description_localisation_string']),
                'authentication_required' => $get['authentication_required'],
                'sortable' => $get_parameters_sortable,
                'searchable' => $get_parameters_searchable,
                'parameters' => $get_parameters
            ]
        ];

        // Minor hack until I come up with a better solution
        if (strlen($routes['GET']['description']) === 0) {
            unset($routes['GET']);
        }

        if (strlen($post['description_localisation_string']) > 0) {
            foreach (array_merge_recursive(Config::get($post['fields_config']), $post['conditionals_config']) as $field => $detail) {
                $detail['title'] = trans($detail['title']);
                $detail['description'] = trans($detail['description']);
                $post_fields[$field] = $detail;
            }

            $routes['POST'] = [
                'description' => trans($post['description_localisation_string']),
                'authentication_required' => $post['authentication_required'],
                'fields' => $post_fields
            ];
        }

        $this->optionsResponse($routes);
    }

    /**
     * Generate the OPTIONS request for the show routes
     *
     * @param array $get Data array to define description_localisation, parameters_config, conditionals and authenticated
     * @param array $delete Data array to define description_localisation and authenticated
     * @param array $patch Data array to define description_localisation, fields_config, conditionals and authenticated
     */
    protected function generateOptionsForShow(
        array $get = [
            'description_localisation_string' => '',
            'parameters_config_string' => '',
            'conditionals_config' => [],
            'authentication_required' => false
        ],
        array $delete = [
            'description_localisation_string' => '',
            'authentication_required' => false
        ],
        array $patch = [
            'description_localisation_string' => '',
            'fields_config' => '',
            'conditionals_config' => [],
            'authentication_required' => false
        ]
    ) {
        $get_parameters = [];
        $patch_fields = [];

        foreach (array_merge_recursive(Config::get($get['parameters_config_string']), $get['conditionals_config']) as $parameter => $detail) {
            $detail['title'] = trans($detail['title']);
            $detail['description'] = trans($detail['description']);
            $get_parameters[$parameter] = $detail;
        }

        $routes = [
            'GET' => [
                'description' => trans($get['description_localisation_string']),
                'authentication_required' => $get['authentication_required'],
                'parameters' => $get_parameters
            ]
        ];

        if (strlen($delete['description_localisation_string']) > 0) {
            $routes['DELETE'] = [
                'description' => trans($delete['description_localisation_string']),
                'authentication_required' => $delete['authentication_required']
            ];
        }

        if (strlen($patch['description_localisation_string']) > 0) {
            foreach (array_merge_recursive(Config::get($patch['fields_config']), $patch['conditionals_config']) as $field => $detail) {
                $detail['title'] = trans($detail['title']);
                $detail['description'] = trans($detail['description']);
                $patch_fields[$field] = $detail;
            }

            $routes['PATCH'] = [
                'description' => trans($patch['description_localisation_string']),
                'authentication_required' => $patch['authentication_required'],
                'fields' => $patch_fields
            ];
        }

        $this->optionsResponse($routes);
    }

    /**
     * Check the request to see if there is anything in the PATCH we need to
     * deal with, checked the entire request for values, assumption being we have
     * already checked the validity of the submitted data
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
