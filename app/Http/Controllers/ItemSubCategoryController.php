<?php

namespace App\Http\Controllers;

use App\Models\ItemSubCategory;
use App\Transformers\ItemSubCategory as ItemSubCategoryTransformer;
use App\Validators\ItemSubCategory as ItemSubCategoryValidator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
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
        $resource_type_id = $this->decodeParameter($resource_type_id);
        $resource_id = $this->decodeParameter($resource_id);
        $item_id = $this->decodeParameter($item_id);
        $item_category_id = $this->decodeParameter($item_category_id);

        $item_sub_category = (new ItemSubCategory())
            ->where('item_id', '=', $item_id)
            ->first();

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            [
                'result' => (new ItemSubCategoryTransformer($item_sub_category))->toArray()
            ],
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
        $resource_type_id = $this->decodeParameter($resource_type_id);
        $resource_id = $this->decodeParameter($resource_id);
        $item_category_id = $this->decodeParameter($item_category_id);
        $item_id = $this->decodeParameter($item_id);
        $item_sub_category_id = $this->decodeParameter($item_sub_category_id);

        $item_sub_category = (new ItemSubCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_sub_category_id);

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            [
                'result' => (new ItemSubCategoryTransformer($item_sub_category))->toArray()
            ],
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
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        return $this->generateOptionsForIndex(
            'descriptions.item_sub_category.GET_index',
            'descriptions.item_sub_category.POST',
            'routes.item_sub_category.fields',
            'routes.item_sub_category.parameters'
        );
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        return $this->generateOptionsForShow(
            'descriptions.item_sub_category.GET_show',
            'descriptions.item_sub_category.DELETE',
            'descriptions.item_sub_category.PATCH',
            'routes.item_sub_category.fields'
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
        $resource_type_id = $this->decodeParameter($resource_type_id);
        $resource_id = $this->decodeParameter($resource_id);
        $item_id = $this->decodeParameter($item_id);
        $item_category_id = $this->decodeParameter($item_category_id);

        $item_sub_category = (new ItemSubCategory())
            ->where('item_id', '=', $item_id)
            ->first();

        if ($item_sub_category !== null) {
            return $this->returnResourceConflict();
        }

        $validator = (new ItemSubCategoryValidator)->create($request);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $item_sub_category = new ItemSubCategory([
                'item_id' => $item_id,
                'sub_category_id' => $this->decodeParameter($request->input('sub_category_id'))
            ]);
            $item_sub_category->save();
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
                'result' => (new ItemSubCategoryTransformer($item_sub_category))->toArray()
            ],
            201
        );
    }
}
