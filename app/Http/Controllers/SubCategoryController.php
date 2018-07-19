<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
