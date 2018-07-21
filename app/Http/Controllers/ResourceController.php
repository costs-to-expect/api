<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceController extends Controller
{
    /**
     * Return all the resources
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $resource_type_id)
    {
        return response()->json(
            [
                'results' => [
                    ['resource_id' => $this->hash->encode(1)],
                    ['resource_id' => $this->hash->encode(2)],
                    ['resource_id' => $this->hash->encode(3)]
                ]
            ],
            200
        );
    }

    /**
     * Return a single resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $resource_type_id, string $resource_id)
    {
        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id
                ]
            ],
            200
        );
    }

    /**
     * Generate the OPTIONS request for the resource list
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request)
    {
        $routes = [
            'GET' => [
                'description' => 'Return the resources for the given resource type',
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['POST'] = [
                'description' => 'Create a new resource',
                'fields' => [
                    [
                        'field' => 'name',
                        'title' => 'Resource name',
                        'description' => 'Enter a name for the resource',
                        'type' => 'string'
                    ],
                    [
                        'field' => 'description',
                        'title' => 'Resource description',
                        'description' => 'Enter a description for the resource',
                        'type' => 'string'
                    ],
                    [
                        'field' => 'effective_date',
                        'title' => 'Resource effective date',
                        'description' => 'Enter an effective date for the resource',
                        'type' => 'date (yyyy-mm-dd)'
                    ]
                ]
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
     * Generate the OPTIONS request for a specific category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request, string $resource_type_id, string $resource_id)
    {
        $routes = [
            'GET' => [
                'description' => 'Return the requested resource',
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['DELETE'] = [
                'description' => 'Delete the requested resource'
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
     * Create a new resource
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, string $resource_type_id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'description' => 'required|string',
                'effective_date' => 'required|date_format:Y-m-d'
            ]
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        return response()->json(
            [
                'result' => [
                    'resource_id' => $this->hash->encode($new_resource_id = 4)
                ]
            ],
            200
        );
    }

    /**
     * Delete a resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, string $resource_type_id, string $resource_id)
    {
        return response()->json(null,204);
    }
}
