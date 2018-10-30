<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\SubCategory;
use App\Transformers\SubCategory as SubCategoryTransformer;
use App\Utilities\Request as UtilityRequest;
use App\Validators\SubCategory as SubCategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
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
     * Return all the sub categories assigned to the given category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $category_id): JsonResponse
    {
        Validate::category($category_id);

        $sub_categories = (new SubCategory())->paginatedCollection($category_id);

        $headers = [
            'X-Total-Count' => count($sub_categories)
        ];

        return response()->json(
            $sub_categories->map(
                function ($sub_category)
                {
                    return (new SubCategoryTransformer($sub_category))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Return a single sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function show(
        Request $request,
        string $category_id,
        string $sub_category_id
    ): JsonResponse
    {
        Validate::subCategory($category_id, $sub_category_id);

        $sub_category = (new SubCategory())->single(
            $category_id,
            $sub_category_id
        );

        if ($sub_category === null) {
            UtilityRequest::notFound();
        }

        return response()->json(
            (new SubCategoryTransformer($sub_category))->toArray(),
            200,
            [
                'X-Total-Count' => 1
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories list
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $category_id): JsonResponse
    {
        Validate::category($category_id);

        return $this->generateOptionsForIndex(
            [
                'description_key' => 'api.descriptions.sub_category.GET_index',
                'parameters_key' => 'api.routes.sub_category.parameters.collection',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'api.descriptions.sub_category.POST',
                'fields_key' => 'api.routes.sub_category.fields',
                'conditionals' => [],
                'authenticated' => true
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the specific sub category
     *
     * @param Request $request
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        Request $request,
        string $category_id,
        string $sub_category_id
    ): JsonResponse
    {
        Validate::subCategory($category_id, $sub_category_id);

        return $this->generateOptionsForShow(
            'api.descriptions.sub_category.GET_show',
            'api.routes.sub_category.parameters.item',
            'api.descriptions.sub_category.DELETE'
        );
    }

    /**
     * Create a new sub category
     *
     * @param Request $request
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function create(Request $request, string $category_id): JsonResponse
    {
        Validate::category($category_id);

        $validator = (new SubCategoryValidator)->create($request, $category_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $sub_category = new SubCategory([
                'category_id' => $category_id,
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);
            $sub_category->save();
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            (new SubCategoryTransformer($sub_category))->toArray(),
            201
        );
    }

    /**
     * Delete the requested sub category
     *
     * @param Request $request,
     * @param string $category_id,
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        string $category_id,
        string $sub_category_id
    ): JsonResponse
    {
        Validate::subCategory($category_id, $sub_category_id);

        $sub_category = (new SubCategory())->single(
            $category_id,
            $sub_category_id
        );

        if ($sub_category === null) {
            UtilityRequest::notFound();
        }

        try {
            $sub_category->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            UtilityRequest::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityRequest::notFound();
        }
    }
}
