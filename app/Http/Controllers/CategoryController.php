<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryController extends Controller
{
    /**
     * Return all the categories
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
                    ['category_id' => $this->hash->encode(1)],
                    ['category_id' => $this->hash->encode(2)],
                    ['category_id' => $this->hash->encode(3)]
                ]
            ],
            200
        );
    }

    /**
     * Return a single category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $category_id)
    {
        return response()->json(
            [
                'result' => [
                    'category_id' => $category_id
                ]
            ],
            200
        );
    }

    /**
     * Generate the OPTIONS request for the category list
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request)
    {
        $routes = [
            'GET' => [
                'description' => 'Return the categories',
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['POST'] = [
                'description' => 'Create a new category',
                'fields' => [
                    [
                        'field' => 'name',
                        'title' => 'Category name',
                        'description' => 'Enter a name for the category'
                    ],
                    [
                        'field' => 'description',
                        'title' => 'Category description',
                        'description' => 'Enter a description for the category'
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
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request, string $category_id)
    {
        $routes = [
            'GET' => [
                'description' => 'Return the requested category',
                'parameters' => []
            ]
        ];

        $options_response = $this->optionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Create a new category
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'description' => 'required|string'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Validation error',
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        return response()->json(
            [
                'result' => [
                    'category_id' => $this->hash->encode($new_category_id = 4)
                ]
            ],
            200
        );
    }
}
