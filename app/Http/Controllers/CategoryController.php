<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Transformers\Category as CategoryTransformer;
use Exception;
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
        $categories = Category::all();

        $headers = [
            'X-Total-Count' => count($categories)
        ];

        $link = $this->generateLinkHeader(10, 0, 20);
        if ($link !== null) {
            $headers['Link'] = $link;
        }

        return response()->json(
            [
                'results' => $categories->map(
                    function ($category)
                    {
                        return (new CategoryTransformer($category))->toArray();
                    }
                )
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
        $category_id = $this->decodeParameter($category_id);

        $category = Category::find($category_id);

        if ($category === null) {
            return $this->returnResourceNotFound();
        }

        return response()->json(
            [
                'result' => (new CategoryTransformer($category))->toArray()
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
            'routes.category.fields',
            'routes.category.parameters'
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
            'routes.category.fields'
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
            Config::get('routes.category.validation.POST')
        );

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $category = new Category([
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);
            $category->save();
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            [
                'result' => (new CategoryTransformer($category))->toArray()
            ],
            201
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
            Config::get('routes.category.validation.PATCH')
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
