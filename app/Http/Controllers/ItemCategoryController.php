<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\Category;
use App\Models\ItemCategory;
use App\Models\Transformers\ItemCategory as ItemCategoryTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Http\Parameters\Request\Validators\ItemCategory as ItemCategoryValidator;
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
        Validate::itemRoute($resource_type_id, $resource_id, $item_id);

        $item_category = (new ItemCategory())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $item_id
        );

        if ($item_category === null) {
            UtilityResponse::notFound();
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
        Validate::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill') {
            UtilityResponse::notFound();
        }

        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            UtilityResponse::notFound();
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
        Validate::itemRoute($resource_type_id, $resource_id, $item_id);

        $this->setConditionalPostParameters($resource_type_id);

        return $this->generateOptionsForIndex(
            [
                'description_key' => 'api.descriptions.item_category.GET_index',
                'parameters_key' => 'api.routes.item_category.parameters.collection',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'api.descriptions.item_category.POST',
                'fields_key' => 'api.routes.item_category.fields',
                'conditionals' => $this->post_parameters,
                'authenticated' => true
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
        Validate::itemRoute($resource_type_id, $resource_id, $item_id);

        if ($item_category_id === 'nill') {
            UtilityResponse::notFound();
        }

        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            UtilityResponse::notFound();
        }

        return $this->generateOptionsForShow(
            [
                'description_key' => 'api.descriptions.item_category.GET_show',
                'parameters_key' => 'api.routes.item_category.parameters.item',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'api.descriptions.item_category.DELETE',
                'authenticated' => true
            ]
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
        Validate::itemRoute($resource_type_id, $resource_id, $item_id);

        $validator = (new ItemCategoryValidator)->create($request);

        $this->setConditionalPostParameters($resource_type_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator, $this->post_parameters);
        }

        try {
            $category_id = $this->hash->decode('category', $request->input('category_id'));

            if ($category_id === false) {
                UtilityResponse::unableToDecode();
            }

            $item_category = new ItemCategory([
                'item_id' => $item_id,
                'category_id' => $category_id
            ]);
            $item_category->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
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
     * @param integer $resource_type_id
     *
     * @return JsonResponse
     */
    private function setConditionalPostParameters($resource_type_id)
    {
        $categories = (new Category())->categoriesByResourceType($resource_type_id);

        $this->post_parameters = ['category_id' => []];
        foreach ($categories as $category) {
            $id = $this->hash->encode('category', $category->category_id);

            if ($id === false) {
                UtilityResponse::unableToDecode();
            }

            $this->post_parameters['category_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $category->category_name,
                'description' => $category->category_description
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
            UtilityResponse::notFound();
        }


        try {
            $item_category->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound();
        }
    }
}
