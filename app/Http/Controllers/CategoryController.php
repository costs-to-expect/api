<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Get;
use App\Http\Parameters\Route\Validate;
use App\Models\Category;
use App\Models\ResourceType;
use App\Transformers\Category as CategoryTransformer;
use App\Utilities\Request as UtilityRequest;
use App\Http\Parameters\Request\Validators\Category as CategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryController extends Controller
{
    protected $collection_parameters = [];
    protected $get_parameters = [];
    protected $post_parameters = [];
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
        $this->collection_parameters = Get::parameters(['include_sub_categories', 'resource_type']);

        $categories = (new Category())->paginatedCollection($this->collection_parameters);

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
        Validate::categoryRoute($category_id);

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
        $this->collection_parameters = Get::parameters(['include_sub_categories', 'resource_type']);

        $this->setConditionalGetParameters();

        $this->setConditionalPostParameters();

        return $this->generateOptionsForIndex(
            [
                'description_key' => 'api.descriptions.category.GET_index',
                'parameters_key' => 'api.routes.category.parameters.collection',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'api.descriptions.category.POST',
                'fields_key' => 'api.routes.category.fields',
                'conditionals' => [],
                'authenticated' => true
            ]
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
        Validate::categoryRoute($category_id);

        return $this->generateOptionsForShow(
            [
                'description_key' => 'api.descriptions.category.GET_show',
                'parameters_key' => 'api.routes.category.parameters.item',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'api.descriptions.category.DELETE',
                'authenticated' => true
            ]
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
            $resource_type_id = $this->hash->decode('resource_type', $request->input('resource_type_id'));

            if ($resource_type_id === false) {
                return response()->json(
                    [
                        'message' => 'Unable to decode parameter or hasher not found'
                    ],
                    500
                );
            }

            $category = new Category([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'resource_type_id' => $resource_type_id
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
            (new CategoryTransformer((new Category)->single($category->id)))->toArray(),
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
        Validate::categoryRoute($category_id);

        try {
            (new Category())->find($category_id)->delete();

            return response()->json([], 204);
        } catch (QueryException $e) {
            UtilityRequest::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityRequest::notFound();
        }
    }

    /**
     * Set any conditional POST parameters, will be merged with the data arrays defined in
     * config/api/route.php
     *
     * @return JsonResponse
     */
    private function setConditionalPostParameters()
    {
        $resource_types = (new ResourceType())->minimisedCollection();

        $this->post_parameters = ['resource_type_id' => []];
        foreach ($resource_types as $resource_type) {
            $id = $this->hash->encode('resource_type', $resource_type->resource_type_id);

            if ($id === false) {
                return response()->json(
                    [
                        'message' => 'Unable to encode parameter or hasher not found'
                    ],
                    500
                );
            }

            $this->post_parameters['resource_type_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $resource_type->name,
                'description' => $resource_type->resource_type_description
            ];
        }
    }

    /**
     * Set any conditional GET parameters, will be merged with the data arrays defined in
     * config/api/route.php
     *
     * @return void
     */
    private function setConditionalGetParameters()
    {
        $this->get_parameters = [
            'resource_type' => [
                'allowed_values' => []
            ]
        ];

        (new ResourceType())->paginatedCollection($this->include_private)->map(
            function ($resource_type)
            {
                $this->get_parameters['resource_type']['allowed_values'][$this->hash->encode('resource_type', $resource_type->id)] = [
                    'value' => $this->hash->encode('resource_type', $resource_type->id),
                    'name' => $resource_type->name,
                    'description' => 'Include results for ' . $resource_type->name . ' resource type'
                ];
            }
        );
    }
}
