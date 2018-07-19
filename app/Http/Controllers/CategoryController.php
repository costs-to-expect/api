<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
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
        return response()->json(
            [
                'GET' => [
                    'description' => 'Return the categories',
                    'parameters' => []
                ]
            ],
            200,
            [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Header' => 'X-Requested-With, Origin, Content-Type, Accept, Authorization',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsShow(Request $request)
    {
        return response()->json(
            [
                'GET' => [
                    'description' => 'Return the requested category',
                    'parameters' => []
                ]
            ],
            200,
            [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Header' => 'X-Requested-With, Origin, Content-Type, Accept, Authorization',
                'Access-Control-Allow-Methods' => 'GET, PATCH, DELETE, OPTIONS',
                'Content-Type' => 'application/json'
            ]
        );
    }
}
