<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use App\Models\SubCategory;
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
        $item_sub_category = (new ItemSubCategory())
            ->where('item_id', '=', $item_id)
            ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                $query->where('resource_id', '=', $resource_id)
                    ->whereHas('resource', function ($query) use ($resource_type_id) {
                        $query->where('resource_type_id', '=', $resource_type_id);
                    });
            })
            ->first();

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
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
        $item_sub_category = (new ItemSubCategory())
            ->where('item_id', '=', $item_id)
            ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                $query->where('resource_id', '=', $resource_id)
                    ->whereHas('resource', function ($query) use ($resource_type_id) {
                        $query->where('resource_type_id', '=', $resource_type_id);
                    });
            })
            ->find($item_sub_category_id);

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
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
        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                $query->where('resource_id', '=', $resource_id)
                    ->whereHas('resource', function ($query) use ($resource_type_id) {
                        $query->where('resource_type_id', '=', $resource_type_id);
                    });
            })
            ->find($item_category_id);

        if ($item_category === null) {
            return $this->returnResourceNotFound();
        }

        $allowed_values = [];
        $item_category = (new ItemCategory())->find($item_category_id);
        if ($item_category_id !== null) {
            $allowed_values = $this->allowedValues($item_category->category_id);
        }

        return $this->generateOptionsForIndex(
            'api.descriptions.item_sub_category.GET_index',
            'api.descriptions.item_sub_category.POST',
            'api.routes.item_sub_category.fields',
            'api.routes.item_sub_category.parameters',
            $allowed_values
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
        $item_sub_category = (new ItemSubCategory())
            ->where('item_id', '=', $item_id)
            ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                $query->where('resource_id', '=', $resource_id)
                    ->whereHas('resource', function ($query) use ($resource_type_id) {
                        $query->where('resource_type_id', '=', $resource_type_id);
                    });
            })
            ->find($item_sub_category_id);

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForShow(
            'api.descriptions.item_sub_category.GET_show',
            'api.descriptions.item_sub_category.DELETE',
            'api.descriptions.item_sub_category.PATCH',
            'api.routes.item_sub_category.fields'
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
        $item_sub_category = (new ItemSubCategory())
            ->where('item_id', '=', $item_id)
            ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                $query->where('resource_id', '=', $resource_id)
                    ->whereHas('resource', function ($query) use ($resource_type_id) {
                        $query->where('resource_type_id', '=', $resource_type_id);
                    });
            })
            ->first();

        if ($item_sub_category !== null) {
            return $this->returnResourceConflict();
        }

        $validator = (new ItemSubCategoryValidator)->create($request, $item_category_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $item_sub_category = new ItemSubCategory([
                'item_id' => $item_id,
                'sub_category_id' => $this->decodeParameter($request->input('sub_category_id'), 'sub_category')
            ]);
            $item_sub_category->save();
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            (new ItemSubCategoryTransformer($item_sub_category))->toArray(),
            201
        );
    }

    /**
     * Generate the array of allowed values fields
     *
     * @param array $category_id
     *
     * @return array
     */
    private function allowedValues($category_id)
    {
        $sub_categories = (new SubCategory())
            ->select('id', 'name', 'description')
            ->where('category_id', '=', $category_id)
            ->get();

        $allowed_values = ['sub_category_id' => []];

        foreach ($sub_categories as $sub_category) {
            $id = $this->encodeParameter($sub_category->id, 'sub_category');
            $allowed_values['sub_category_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $sub_category->name,
                'description' => $sub_category->description
            ];
        }

        return $allowed_values;
    }
}
