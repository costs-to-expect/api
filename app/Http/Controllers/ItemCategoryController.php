<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Transformers\ItemCategory as ItemCategoryTransformer;
use App\Validators\ItemCategory as ItemCategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
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
            ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                $query->where('resource_id', '=', $resource_id)
                    ->whereHas('resource', function ($query) use ($resource_type_id) {
                        $query->where('resource_type_id', '=', $resource_type_id);
                    });
            })
            ->first();

        if ($item_category === null) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemCategoryTransformer($item_category))->toArray(),
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

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemCategoryTransformer($item_category))->toArray(),
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
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        $item = (new Item())
            ->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->find($item_id);

        if ($item === null) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForIndex(
            'api.descriptions.item_category.GET_index',
            'api.descriptions.item_category.POST',
            'api.routes.item_category.fields',
            'api.routes.item_category.parameters',
            $this->allowedValues()
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
     *
     * @return JsonResponse
     */
    public function optionsShow(
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

        return $this->generateOptionsForShow(
            'api.descriptions.item_category.GET_show',
            'api.descriptions.item_category.DELETE',
            'api.descriptions.item_category.PATCH',
            'api.routes.item_category.fields'
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
        $item = (new Item())
            ->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->find($item_id);

        if ($item === null) {
            return $this->returnResourceNotFound();
        }

        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->first();

        if ($item_category !== null) {
            return $this->returnResourceConflict();
        }

        $validator = (new ItemCategoryValidator)->create($request);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator, $this->allowedValues());
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
                    'message' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            (new ItemCategoryTransformer($item_category))->toArray(),
            201
        );
    }

    /**
     * Generate the array of allowed values fields
     *
     * @return array
     */
    private function allowedValues()
    {
        $categories = (new Category())->select('id', 'name', 'description')->get();

        $allowed_values = ['category_id' => []];
        foreach ($categories as $category) {
            $id = $this->encodeParameter($category->id, 'category');

            $allowed_values['category_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $category->name,
                'description' => $category->description
            ];
        }

        return $allowed_values;
    }

    /**
     * Delete the assigned category
     *
     * @param Request $request,
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function delete(
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


        try {
            $item_category->delete();

            return response()->json([],204);
        } catch (QueryException $e) {
            return $this->returnForeignKeyConstraintError();
        } catch (Exception $e) {
            return $this->returnResourceNotFound();
        }
    }
}
