<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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
        $headers = [
            'X-Total-Count' => 30,
        ];

        $link = $this->generateLinkHeaderValue(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            [
                'results' => [
                    ['category_id' => $this->hash->encode(1)],
                    ['category_id' => $this->hash->encode(2)],
                    ['category_id' => $this->hash->encode(3)]
                ]
            ],
            200,
            $headers
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
            200,
            [
                'X-Total-Count' => 1
            ]
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
        return $this->generateOptionsForIndex(
            'descriptions.category.GET_index',
            'descriptions.category.POST',
            'fields.category.fields'
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
        return $this->generateOptionsForShow(
            'descriptions.category.GET_show',
            'descriptions.category.DELETE',
            'descriptions.category.PATCH',
            'fields.category.fields'
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
            Config::get('fields.category.validation.POST')
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
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

    /**
     * Delete a category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, string $category_id)
    {
        return response()->json(null,204);
    }

    /**
     * Update the request category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $category_id)
    {
        $validator = Validator::make(
            $request->all(),
            Config::get('fields.category.validation.PATCH')
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
                    'category_id' => $category_id
                ]
            ],
            200
        );
    }
}
