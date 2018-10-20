<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\Category;
use App\Models\ItemCategory;
use App\Transformers\ItemCategory as ItemCategoryTransformer;
use App\Utilities\Request as UtilityRequest;
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
    private $post_parameters = [];

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
        Validate::item($resource_type_id, $resource_id, $item_id);

        $item_category = (new ItemCategory())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $item_id
        );

        if ($item_category === null) {
            UtilityRequest::notFound();
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
        Validate::item($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill') {
            UtilityRequest::notFound();
        }

        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            UtilityRequest::notFound();
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
        Validate::item($resource_type_id, $resource_id, $item_id);

        $this->setConditionalPostParameters();

        return $this->generateOptionsForIndex(
            'api.descriptions.item_category.GET_index',
            'api.routes.item_category.parameters.collection',
            'api.descriptions.item_category.POST',
            'api.routes.item_category.fields',
            $this->post_parameters
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
        Validate::item($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill') {
            UtilityRequest::notFound();
        }

        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            UtilityRequest::notFound();
        }

        return $this->generateOptionsForShow(
            'api.descriptions.item_category.GET_show',
            'api.routes.item_category.parameters.item',
            'api.descriptions.item_category.DELETE'
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
        Validate::item($resource_type_id, $resource_id, $item_id);

        $validator = (new ItemCategoryValidator)->create($request);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator, $this->allowedValues());
        }

        try {
            $category_id = $this->hash->decode('category', $request->input('category_id'));

            if ($category_id === false) {
                return response()->json(
                    [
                        'message' => 'Unable to decode parameter or hasher not found'
                    ],
                    500
                );
            }

            $item_category = new ItemCategory([
                'item_id' => $item_id,
                'category_id' => $category_id
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
     * Set any conditional POST parameters, will be merged with the data arrays defined in
     * config/api/route.php
     *
     * @return JsonResponse
     */
    private function setConditionalPostParameters()
    {
        $categories = (new Category())->select('id', 'name', 'description')->get();

        $this->post_parameters = ['category_id' => []];
        foreach ($categories as $category) {
            $id = $this->hash->encode('category', $category->id);

            if ($id === false) {
                return response()->json(
                    [
                        'message' => 'Unable to encode parameter or hasher not found'
                    ],
                    500
                );
            }

            $this->post_parameters['category_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $category->name,
                'description' => $category->description
            ];
        }
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
        Validate::itemCategory($resource_type_id, $resource_id, $item_id, $item_category_id);

        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            UtilityRequest::notFound();
        }


        try {
            $item_category->delete();

            return response()->json([],204);
        } catch (QueryException $e) {
            UtilityRequest::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityRequest::notFound();
        }
    }
}
