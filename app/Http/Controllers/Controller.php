<?php

namespace App\Http\Controllers;

use Hashids\Hashids;
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

    protected $hashers;

    public function __construct()
    {
        $min_length = Config::get('api.hashids.min_length');

        $this->hashers['category'] = new Hashids(Config::get('api.hashids.category'), $min_length);
        $this->hashers['_sub_category'] = new Hashids(Config::get('api.hashids.sub_category'), $min_length);
        $this->hashers['resource_type'] = new Hashids(Config::get('api.hashids.resource_type'), $min_length);
        $this->hashers['_resource'] = new Hashids(Config::get('api.hashids.resource'), $min_length);
        $this->hashers['item'] = new Hashids(Config::get('api.hashids.item'), $min_length);
        $this->hashers['item_category'] = new Hashids(Config::get('api.hashids.item_category'), $min_length);
        $this->hashers['item_sub_category'] = new Hashids(Config::get('api.hashids.item_sub_category'), $min_length);
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
     * @return JsonResponse
     */
    protected function returnValidationErrors(Validator $validator): JsonResponse
    {
        return response()->json(
            [
                'error' => 'Validation error',
                'fields' => $validator->errors()
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
                'error' => 'Resource not found'
            ],
            404
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
                'error' => 'Value already set, conflict'
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
     *
     * @return JsonResponse
     */
    protected function generateOptionsForIndex(
        string $get_description_key,
        string $post_description_key,
        string $post_fields_key,
        string $parameters_key
    ): JsonResponse
    {
        $routes = [
            'GET' => [
                'description' => Config::get($get_description_key),
                'authenticated' => false,
                'parameters' => Config::get($parameters_key)
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

    /**
     * Generate the OPTIONS request for the show routes
     *
     * @param string $get_description_key
     * @param string $delete_description_key
     * @param string $patch_description_key
     * @param string $patch_fields_key
     *
     * @return JsonResponse
     */
    protected function generateOptionsForShow(
        string $get_description_key,
        string $delete_description_key,
        string $patch_description_key,
        string $patch_fields_key
    ): JsonResponse
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

    /**
     * Generate the Link header value based on the value of $previous_start, $next_start and $per_page
     *
     * @param integer $limit
     * @param integer|null $offset_prev
     * @param integer|null $offset_next
     *
     * @return string|null
     */
    protected function generateLinkHeader(int $limit, int $offset_prev = null, int $offset_next = null): ?string
    {
        $link = '';

        if ($offset_prev !== null) {
            $link .= '<' . Config::get('api.app.url') . '/' . Config::get('api.version.prefix') . '/categories?offset=' . $offset_prev . '&limit=' .
                $limit . '>; rel="prev"';
        }

        if ($offset_next !== null) {
            if (strlen($link) > 0) {
                $link .= ', ';
            }

            $link .= '<' . Config::get('api.app.url') . '/' . Config::get('api.version.prefix')  . '/categories?offset=' . $offset_next . '&limit=' .
                $limit . '>; rel="next"';
        }

        if (strlen($link) > 0) {
            return $link;
        } else {
            return null;
        }
    }

    /**
     * Decode a get param and return the integer
     *
     * @param string $parameter The hash to decode
     * @param string $hasher to use to decode
     *
     * @return int|JsonResponse
     */
    protected function decodeParameter(string $parameter, $hasher)
    {
        if (array_key_exists($hasher, $this->hashers) === true) {
            $id = $this->$this->hashers[$hasher]->decode($parameter);
            if (is_array($id) && array_key_exists(0, $id)) {
                return $id[0];
            } else {
                return $this->returnResourceNotFound();
            }
        } else {
            return response()->json(
                [
                    'error' => 'Hasher not found'
                ],
                500
            );
        }
    }
}
