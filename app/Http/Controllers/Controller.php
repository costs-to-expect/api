<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

    protected $collection_parameters = [];
    protected $parameters_show = [];

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * Generate the options response for an endpoint
     *
     * @param array $verbs Verb arrays
     * @param integer $http_status_code, defaults to 200
     *
     * @return array Three indexes, verbs, status and headers
     */
    protected function generateOptionsResponse(array $verbs, $http_status_code = 200): array
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
     * @return JsonResponse
     */
    protected function requireAtLeastOneFieldToPatch(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Bad request, you need to supply at least one field to patch'
            ],
            400
        );
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
     * Return 404, resource not found
     *
     * @return JsonResponse
     */
    protected function returnResourceNotFound(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Resource not found'
            ],
            404
        );
    }

    /**
     * Return 404, resource not found
     *
     * @return JsonResponse
     */
    protected function returnForeignKeyConstraintError(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Unable to delete resource, dependant data exists'
            ],
            500
        );
    }

    /**
     * Return 409, resource value already set
     *
     * @return JsonResponse
     */
    protected function returnResourceConflict(): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Value already set, conflict'
            ],
            409
        );
    }

    /**
     * Generate the OPTIONS request for the index routes
     *
     * @param string $get_description_key
     * @param string $post_description_key
     * @param string $post_fields_key
     * @param string $parameters_key
     * @param array $post_fields Conditionally set POST fields, typically used to set allowed values
     * @param array $get_parameters Conditionally set GET parameters, typically used to set allowed values
     *
     * @return JsonResponse
     */
    protected function generateOptionsForIndex(
        string $get_description_key,
        string $post_description_key,
        string $post_fields_key,
        string $parameters_key,
        array $post_fields = [],
        array $get_parameters = []
    ): JsonResponse
    {
        $routes = [
            'GET' => [
                'description' => Config::get($get_description_key),
                'authenticated' => false,
                'parameters' => array_merge_recursive(Config::get($parameters_key), $get_parameters)
            ],
            'POST' => [
                'description' => Config::get($post_description_key),
                'authenticated' => true,
                'fields' => array_merge_recursive(Config::get($post_fields_key), $post_fields)
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Generate the OPTIONS request for the show routes
     *
     * @param string $get_description_key
     * @param string $delete_description_key
     * @param string $patch_description_key
     * @param string $patch_fields_key
     * @param string $parameters_key
     *
     * @return JsonResponse
     */
    protected function generateOptionsForShow(
        string $get_description_key,
        string $delete_description_key,
        string $patch_description_key,
        string $patch_fields_key,
        string $parameters_key
    ): JsonResponse
    {
        $routes = [
            'GET' => [
                'description' => Config::get($get_description_key),
                'authenticated' => false,
                'parameters' => Config::get($parameters_key)
            ],
            'DELETE' => [
                'description' => Config::get($delete_description_key),
                'authenticated' => true,
            ],
            /*'PATCH' => [
                'description' => Config::get($patch_description_key),
                'authenticated' => true,
                'fields' => Config::get($patch_fields_key)
            ]*/
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Generate the Link header value based on the value of $previous_start, $next_start and $per_page
     *
     * @param string $uri
     * @param string $parameters
     * @param integer $limit
     * @param integer|null $offset_prev
     * @param integer|null $offset_next
     *
     * @return string|null
     */
    protected function generateLinkHeader(string $uri, string $parameters, int $limit, int $offset_prev = null, int $offset_next = null): ?string
    {
        $uri .= '?';

        if (strlen($parameters) > 0) {
            $uri .= $parameters . '&';
        }

        $link = '';

        if ($offset_prev !== null) {
            $link .= '<' . Config::get('api.app.url') . '/' . $uri . 'offset=' . $offset_prev . '&limit=' .
                $limit . '>; rel="prev"';
        }

        if ($offset_next !== null) {
            if (strlen($link) > 0) {
                $link .= ', ';
            }

            $link .= '<' . Config::get('api.app.url') . '/' . $uri . 'offset=' . $offset_next . '&limit=' .
                $limit . '>; rel="next"';
        }

        if (strlen($link) > 0) {
            return $link;
        } else {
            return null;
        }
    }

    /**
     * Check the $request for GET parameters, if any are valid set them the the
     * parameters collection
     *
     * @param array $request_parameters Parameters from $request->all()
     * @param array $parameters GET params to try and set
     *
     * @return void
     */
    protected function setCollectionParameters(array $request_parameters = [], array $parameters = [])
    {
        $this->collection_parameters = [];

        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter, $request_parameters) === true &&
                $request_parameters[$parameter] !== null &&
                $request_parameters[$parameter] !== 'nill') {
                $this->collection_parameters[$parameter] = $request_parameters[$parameter];
            }
        }

        $this->validateCollectionParameters($parameters);
    }

    /**
     * Validate collection parameters, invalid collection parameters are silently removed
     *
     * @param array $parameters GET parameters to attempt to validate
     *
     * @return void
     */
    protected function validateCollectionParameters(array $parameters = [])
    {
        foreach ($parameters as $parameter) {
            switch ($parameter) {
                case 'category':
                    if (array_key_exists($parameter, $this->collection_parameters) === true) {
                        if ((new Category())->where('id', '=', $this->collection_parameters[$parameter])->exists() === false) {
                            unset($this->collection_parameters[$parameter]);
                        }
                    }
                    break;

                default:
                    // Do nothing
                    break;
            }
        }

        if (array_key_exists('category', $this->collection_parameters) === true) {
            if ((new Category())->where('id', '=', $this->collection_parameters['category'])->exists() === false) {
                unset($this->collection_parameters['category']);
            }
        }

        var_dump($this->collection_parameters);
    }
}
