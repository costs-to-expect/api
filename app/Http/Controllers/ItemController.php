<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Manage items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemController extends Controller
{
    /**
     * Return all the items
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $resource_type_id, string $resource_id)
    {
        return response()->json(
            [
                'results' => [
                    ['item_id' => $this->hash->encode(1)],
                    ['item_id' => $this->hash->encode(2)],
                    ['item_id' => $this->hash->encode(3)]
                ]
            ],
            200
        );
    }

    /**
     * Return a single item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $resource_type_id, string $resource_id, string $item_id)
    {
        return response()->json(
            [
                'result' => [
                    'resource_type_id' => $resource_type_id,
                    'resource_id' => $resource_id,
                    'item_id' => $item_id
                ]
            ],
            200
        );
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id, string $resource_id)
    {
        $routes = [
            'GET' => [
                'description' => 'Return the items for the given resource',
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['POST'] = [
                'description' => 'Create a new resource',
                'fields' => [
                    [
                        'field' => 'description',
                        'title' => 'Item description',
                        'description' => 'Enter a description for the item',
                        'type' => 'string'
                    ],
                    [
                        'field' => 'effective_date',
                        'title' => 'Item effective date',
                        'description' => 'Enter the effective date for the item',
                        'type' => 'date (yyyy-mm-dd)'
                    ],
                    [
                        'field' => 'total',
                        'title' => 'Resource total',
                        'description' => 'Enter the total amount for the item',
                        'type' => 'decimal (10,2)'
                    ],
                    [
                        'field' => 'percentage',
                        'title' => 'Resource effective date',
                        'description' => 'Enter the percentage to allot, defaults to 100',
                        'type' => 'string'
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
     * Generate the OPTIONS request for a specific item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request, string $resource_type_id, string $resource_id, string $item_id)
    {
        $routes = [
            'GET' => [
                'description' => 'Return the requested item',
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['DELETE'] = [
                'description' => 'Delete the requested item'
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
     * Create a new item
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, string $resource_type_id, string $resource_id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'description' => 'required|string',
                'effective_date' => 'required|date_format:Y-m-d',
                'total' => 'required|regex:/^\d+\.\d{2}$/',
                'percentage' => 'required|integer|between:1,100'
            ]
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        return response()->json(
            [
                'result' => [
                    'item_id' => $this->hash->encode($new_item_id = 4)
                ]
            ],
            200
        );
    }

    /**
     * Delete an item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, string $resource_type_id, string $resource_id, string $item_id)
    {
        return response()->json(null, 204);
    }
}
