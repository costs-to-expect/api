<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubCategoryController extends Controller
{
    /**
     * Return all the sub categories assigned to the given category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $category_id)
    {
        return response()->json(
            [
                'results' => [
                    ['sub_category_id' => $this->hash->encode(1)],
                    ['sub_category_id' => $this->hash->encode(2)],
                    ['sub_category_id' => $this->hash->encode(3)]
                ]
            ],
            200
        );
    }

    /**
     * Return a single sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $category_id, string $sub_category_id)
    {
        return response()->json(
            [
                'result' => [
                    'category_id' => $category_id,
                    'sub_category_id' => $sub_category_id
                ]
            ],
            200
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories list
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request, string $category_id)
    {
        $routes = [
            'GET' => [
                'description' => Config::get('descriptions.sub_category.GET_index'),
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['POST'] = [
                'description' => Config::get('descriptions.sub_category.POST'),
                'fields' => Config::get('fields.sub_category.fields')
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
     * Generate the OPTIONS request for the specific sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request, string $category_id, string $sub_category_id)
    {
        $routes = [
            'GET' => [
                'description' => Config::get('descriptions.sub_category.GET_show'),
                'parameters' => []
            ]
        ];

        if (Auth::guard('api')->check() === true) {
            $routes['DELETE'] = [
                'description' => Config::get('descriptions.sub_category.DELETE'),
            ];

            $routes['PATCH'] = [
                'description' => Config::get('descriptions.sub_category.PATCH'),
                'fields' => Config::get('fields.sub_category.fields')
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
     * Create a new sub category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, string $category_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('fields.sub_category.validation.PATCH')
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        return response()->json(
            [
                'result' => [
                    'category_id' => $category_id,
                    'sub_category_id' => $this->hash->encode($new_sub_category_id = 4)
                ]
            ],
            200
        );
    }

    /**
     * Delete a sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, string $category_id, string $sub_category_id)
    {
        return response()->json(null,204);
    }

    /**
     * Update the request sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $category_id, string $sub_category_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('fields.sub_category.validation.PATCH')
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
                    'category_id' => $category_id,
                    'sub_category_id' => $sub_category_id
                ]
            ],
            200
        );
    }
}
