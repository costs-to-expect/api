<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Transformers\Category as CategoryTransformer;
use App\Validators\Category as CategoryValidator;
use Exception;
use Illuminate\Http\JsonResponse;
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
     * Return all the categories
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
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
            $categories->map(
                function ($category)
                {
                    return (new CategoryTransformer($category))->toArray();
                }
            ),
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
     * @return JsonResponse
     */
    public function show(Request $request, $category_id): JsonResponse
    {
        $category = (new Category)->find($category_id);

        if ($category === null) {
            return $this->returnResourceNotFound();
        }

        return response()->json(
            (new CategoryTransformer($category))->toArray(),
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
     * @return JsonResponse
     */
    public function optionsIndex(Request $request): JsonResponse
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
     * @return JsonResponse
     */
    public function optionsShow(Request $request, string $category_id): JsonResponse
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
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = (new CategoryValidator)->create($request);

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
            (new CategoryTransformer($category))->toArray(),
            201
        );
    }

    /**
     * Delete a category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function delete(Request $request, string $category_id): JsonResponse
    {
        return response()->json(null,204);
    }

    /**
     * Update the request category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function update(Request $request, string $category_id): JsonResponse
    {
        $validator = (new CategoryValidator)->update($request, $category_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        if (count($request->all()) === 0) {
            return $this->requireAtLeastOneFieldToPatch();
        }

        return response()->json(
            [
                'category_id' => $category_id
            ],
            200
        );
    }
}
