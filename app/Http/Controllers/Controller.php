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

    /**
     * @var boolean Allow the entire collection to be returned ignoring pagination
     */
    protected $allow_entire_collection = false;

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
