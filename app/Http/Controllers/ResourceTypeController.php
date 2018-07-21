<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    /**
     * Return all the resource types
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(
            [
                'results' => [
                    ['resource_type_id' => $this->hash->encode(1)],
                    ['resource_type_id' => $this->hash->encode(2)],
                    ['resource_type_id' => $this->hash->encode(3)]
                ]
            ],
            200
        );
    }

    /**
     * Return a single resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $resource_type_id)
    {
        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $resource_type_id
                ]
            ],
            200
        );
    }

    /**
     * Generate the OPTIONS request for the resource type list
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request)
    {
        $routes = [
            'GET' => [
                'description' => 'Return the resource types',
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['POST'] = [
                'description' => 'Create a new resource type',
                'fields' => Config::get('fields.resource_type.fields')
            ];
        }

        $options_response = $this->optionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Generate the OPTIONS request fir a specific resource type
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request)
    {
        $routes = [
            'GET' => [
                'description' => 'Return the requested resource type',
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['DELETE'] = [
                'description' => 'Delete the requested resource type'
            ];

            $routes['PATCH'] = [
                'description' => 'Update the requested resource type',
                'fields' => Config::get('fields.resource_type.fields')
            ];
        }

        $options_response = $this->optionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Create a new resource type
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('fields.resource_type.validation.POST')
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        return response()->json(
            [
                'result' => [
                    'category_id' => $this->hash->encode($new_resource_id = 4)
                ]
            ],
            200
        );
    }

    /**
     * Delete a resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, string $resource_type_id)
    {
        return response()->json(null,204);
    }

    /**
     * Update the request resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $resource_type_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('fields.resource_type.validation.PATCH')
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        if (count($request->all()) === 0) {
            return $this->requireAtLeastOneFieldToPatch();
        }

        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $resource_type_id
                ]
            ],
            200
        );
    }
}
