<?php

namespace App\Http\Controllers;

use App\Validators\Request\Route;
use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use App\Models\SubCategory;
use App\Models\Transformers\ItemSubCategory as ItemSubCategoryTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\ItemSubCategory as ItemSubCategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubCategoryController extends Controller
{
    /**
     * Return the sub category assigned to an item
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function index(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        $item_sub_category = (new ItemSubCategory())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_sub_category === null) {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            [(new ItemSubCategoryTransformer($item_sub_category))->toArray()],
            200,
            $headers
        );
    }

    /**
     * Return a single item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     * @param string $item_sub_category_id
     *
     * @return JsonResponse
     */
    public function show(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_sub_category_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill' || $item_sub_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        $item_sub_category = (new ItemSubCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_sub_category_id
        );

        if ($item_sub_category === null) {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemSubCategoryTransformer($item_sub_category))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        $item_category = (new ItemCategory())->find($item_category_id);
        if ($item_category === null) {
            UtilityResponse::notFound(trans('entities.item-category'));
        }

        return $this->generateOptionsForIndex(
            [
                'description_localisation_string' => 'route-descriptions.item_sub_category_GET_index',
                'parameters_config_string' => 'api.item-subcategory.parameters.collection',
                'conditionals_config' => [],
                'sortable_config' => null,
                'searchable_config' => null,
                'enable_pagination' => false,
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.item_sub_category_POST',
                'fields_config' => 'api.item-subcategory.fields',
                'conditionals_config' => $this->conditionalPostParameters($item_category->category_id),
                'authentication_required' => true
            ]
        );
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     * @param string $item_sub_category_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_sub_category_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill' || $item_sub_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        $item_sub_category = (new ItemSubCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_sub_category_id
        );

        if ($item_sub_category === null) {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        return $this->generateOptionsForShow(
            [
                'description_localisation_string' => 'route-descriptions.item_sub_category_GET_show',
                'parameters_config_string' => 'api.item-subcategory.parameters.item',
                'conditionals_config' => [],
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.item_sub_category_DELETE',
                'authentication_required' => true
            ]
        );
    }

    /**
     * Assign the sub category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function create(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_category_id);

        $validator = (new ItemSubCategoryValidator)->create($request, $item_category->category_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator, $this->conditionalPostParameters($item_category_id));
        }

        try {
            $sub_category_id = $this->hash->decode('subcategory', $request->input('sub_category_id'));

            if ($sub_category_id === false) {
                UtilityResponse::unableToDecode();
            }

            $item_sub_category = new ItemSubCategory([
                'item_category_id' => $item_category_id,
                'sub_category_id' => $sub_category_id
            ]);
            $item_sub_category->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemSubCategoryTransformer($item_sub_category))->toArray(),
            201
        );
    }

    /**
     * Generate any conditional POST parameters, will be merged with the data
     * arrays defined in config/api/[type]/fields.php
     *
     * @param integer $category_id
     *
     * @return array
     */
    private function conditionalPostParameters($category_id): array
    {
        $sub_categories = (new SubCategory())
            ->select('id', 'name', 'description')
            ->where('category_id', '=', $category_id)
            ->get();

        $conditional_post_parameters = ['sub_category_id' => []];

        foreach ($sub_categories as $sub_category) {
            $id = $this->hash->encode('subcategory', $sub_category->id);

            if ($id === false) {
                UtilityResponse::unableToDecode();
            }

            $conditional_post_parameters['sub_category_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $sub_category->name,
                'description' => $sub_category->description
            ];
        }

        return $conditional_post_parameters;
    }

    /**
     * Delete the assigned sub category
     *
     * @param Request $request,
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id,
     * @param string $item_sub_category_id
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_sub_category_id
    ): JsonResponse
    {
        Route::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill' || $item_sub_category_id === 'nill') {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        $item_sub_category = (new ItemSubCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_sub_category_id
        );

        if ($item_sub_category === null) {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }

        try {
            $item_sub_category->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.item-sub-category'));
        }
    }
}
