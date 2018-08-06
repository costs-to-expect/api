<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Transformers\Category as CategoryTransformer;
use App\Validators\Category as CategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
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
        $categories = (new Category())->paginatedCollection();

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
            'api.descriptions.category.GET_index',
            'api.descriptions.category.POST',
            'api.routes.category.fields',
            'api.routes.category.parameters'
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
        if ((new Category)->find($category_id) === null) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForShow(
            'api.descriptions.category.GET_show',
            'api.descriptions.category.DELETE',
            'api.descriptions.category.PATCH',
            'api.routes.category.fields'
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
                    'message' => 'Error creating new record'
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
     * Delete the requested category
     *
     * @param Request $request,
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        string $category_id
    ): JsonResponse
    {
        $category = (new Category())->find($category_id);

        if ($category === null) {
            return $this->returnResourceNotFound();
        }

        try {
            $category->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            return $this->returnForeignKeyConstraintError();
        } catch (Exception $e) {
            return $this->returnResourceNotFound();
        }
    }
}
