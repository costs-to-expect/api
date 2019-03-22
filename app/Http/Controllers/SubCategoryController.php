<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\SubCategory;
use App\Models\Transformers\SubCategory as SubCategoryTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Http\Parameters\Request\Validators\SubCategory as SubCategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
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
        Validate::categoryRoute($category_id);

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
        Validate::subCategoryRoute($category_id, $sub_category_id);

        $sub_category = (new SubCategory())->single(
            $category_id,
            $sub_category_id
        );

        if ($sub_category === null) {
            UtilityResponse::notFound();
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
        Validate::categoryRoute($category_id);

        return $this->generateOptionsForIndex(
            [
                'description_key' => 'route-descriptions.sub_category_GET_index',
                'parameters_key' => 'api.parameters-and-fields.sub_category.parameters.collection',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'route-descriptions.sub_category_POST',
                'fields_key' => 'api.parameters-and-fields.sub_category.fields',
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
        Validate::subCategoryRoute($category_id, $sub_category_id);

        return $this->generateOptionsForShow(
            [
                'description_key' => 'route-descriptions.sub_category_GET_show',
                'parameters_key' => 'api.parameters-and-fields.sub_category.parameters.item',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'route-descriptions.sub_category_DELETE',
                'authenticated' => true
            ]
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
        Validate::categoryRoute($category_id);

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
            UtilityResponse::failedToSaveModelForCreate();
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
        Validate::subCategoryRoute($category_id, $sub_category_id);

        $sub_category = (new SubCategory())->single(
            $category_id,
            $sub_category_id
        );

        if ($sub_category === null) {
            UtilityResponse::notFound(trans('entities.sub-category'));
        }

        try {
            $sub_category->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.sub-category'));
        }
    }
}
