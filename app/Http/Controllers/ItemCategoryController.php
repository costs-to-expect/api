<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use App\Transformers\ItemCategory as ItemCategoryTransformer;
use App\Validators\ItemCategory as ItemCategoryValidator;
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
class ItemCategoryController extends Controller
{
    /**
     * Return the category assigned to an item
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->first();

        if ($item_category === null) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            [
                'result' => (new ItemCategoryTransformer($item_category))->toArray()
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
     *
     * @return JsonResponse
     */
    public function show(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_category_id);

        if ($item_category === null) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            [
                'result' => (new ItemCategoryTransformer($item_category))->toArray()
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
            'descriptions.item_category.GET_index',
            'descriptions.item_category.POST',
            'routes.item_category.fields',
            'routes.item_category.parameters'
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
            'descriptions.item_category.GET_show',
            'descriptions.item_category.DELETE',
            'descriptions.item_category.PATCH',
            'routes.item_category.fields'
        );
    }

    /**
     * Assign the category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function create(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->first();

        if ($item_category !== null) {
            return $this->returnResourceConflict();
        }

        $validator = (new ItemCategoryValidator)->create($request);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $item_category = new ItemCategory([
                'item_id' => $item_id,
                'category_id' => $this->decodeParameter($request->input('category_id'), 'category')
            ]);
            $item_category->save();
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
                'result' => (new ItemCategoryTransformer($item_category))->toArray()
            ],
            201
        );
    }
}
