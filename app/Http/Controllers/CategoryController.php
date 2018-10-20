<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Get;
use App\Http\Parameters\Route\Validate;
use App\Models\Category;
use App\Transformers\Category as CategoryTransformer;
use App\Utilities\Request as UtilityRequest;
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
    protected $collection_parameters = [];
    protected $show_parameters = [];

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

        $this->collection_parameters = Get::parameters(['include_sub_categories']);

        $headers = [
            'X-Total-Count' => count($categories)
        ];

        return response()->json(
            $categories->map(
                function ($category)
                {
                    return (new CategoryTransformer($category, $this->collection_parameters))->toArray();
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
        Validate::category($category_id);

        $this->show_parameters = Get::parameters(['include_sub_categories']);

        $category = (new Category)->single($category_id);

        if ($category === null) {
            UtilityRequest::notFound();
        }

        return response()->json(
            (new CategoryTransformer($category, $this->show_parameters))->toArray(),
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
            'api.routes.category.parameters.collection',
            'api.descriptions.category.POST',
            'api.routes.category.fields'
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
        Validate::category($category_id);

        return $this->generateOptionsForShow(
            'api.descriptions.category.GET_show',
            'api.routes.category.parameters.item',
            'api.descriptions.category.DELETE'
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
        Validate::category($category_id);

        try {
            (new Category())->find($category_id)->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            UtilityRequest::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityRequest::notFound();
        }
    }
}
