<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request)
    {
        $options_response = $this->optionsResponse(
            [
                'GET' => [
                    'description' => 'Return the categories',
                    'parameters' => []
                ]
            ]
        );

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * @param Request $request
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request, string $category_id)
    {
        $options_response = $this->optionsResponse(
            [
                'GET' => [
                    'description' => 'Return the requested category',
                    'parameters' => []
                ]
            ]
        );

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'error'=>$validator->errors()
                ],
                400
            );
        }
    }
}
