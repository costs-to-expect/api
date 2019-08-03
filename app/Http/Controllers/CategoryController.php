<?php

namespace App\Http\Controllers;

use App\Utilities\Pagination as UtilityPagination;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\Category;
use App\Models\ResourceType;
use App\Models\Transformers\Category as CategoryTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\Category as CategoryValidator;
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
    protected $show_parameters = [];

    /**
     * Return the categories collection
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $parameters = Parameters::fetch(['include-subcategories']);

        $total = (new Category())->totalCount(
            $this->include_private,
            $parameters
        );

        $pagination = UtilityPagination::init(request()->path(), $total)
            ->setParameters($parameters)
            ->paging();

        $categories = (new Category())->paginatedCollection(
            $this->include_private,
            $parameters,
            $pagination['offset'],
            $pagination['limit']
        );

        $headers = [
            'X-Count' => count($categories),
            'X-Total-Count' => $total,
            'X-Offset' => $pagination['offset'],
            'X-Limit' => $pagination['limit'],
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next']
        ];

        return response()->json(
            array_map(
                function($category) use ($parameters) {
                    return (new CategoryTransformer($category, $parameters))->toArray();
                },
                $categories
            ),
            200,
            $headers
        );
    }

    /**
     * Return a single category
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function show($category_id): JsonResponse
    {
        Route::categoryRoute($category_id);

        $this->show_parameters = Parameters::fetch(['include-subcategories']);

        $category = (new Category)->single($category_id);

        if ($category === null) {
            UtilityResponse::notFound(trans('entities.category'));
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
        $this->collection_parameters = Parameters::fetch(['include-subcategories']);

        return $this->generateOptionsForIndex(
            [
                'description_localisation_string' => 'route-descriptions.category_GET_index',
                'parameters_config_string' => 'api.category.parameters.collection',
                'conditionals_config' => [],
                'sortable_config' => null,
                'searchable_config' => null,
                'enable_pagination' => true,
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.category_POST',
                'fields_config' => 'api.category.fields',
                'conditionals_config' => $this->conditionalPostParameters(),
                'authentication_required' => true
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
        Route::categoryRoute($category_id);

        return $this->generateOptionsForShow(
            [
                'description_localisation_string' => 'route-descriptions.category_GET_show',
                'parameters_config_string' => 'api.category.parameters.item',
                'conditionals_config' => [],
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.category_DELETE',
                'authentication_required' => true
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
        $validator = (new CategoryValidator)->create();

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $resource_type_id = $this->hash->decode('resource_type', $request->input('resource_type_id'));

            if ($resource_type_id === false) {
                UtilityResponse::unableToDecode();
            }

            $category = new Category([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'resource_type_id' => $resource_type_id
            ]);
            $category->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
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
        Route::categoryRoute($category_id);

        try {
            (new Category())->find($category_id)->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.category'));
        }
    }

    /**
     * Define any conditional POST parameters/allowed values, will be passed into
     * the relevant options method to merge with the definition array
     */
    private function conditionalPostParameters(): array
    {
        $resource_types = (new ResourceType())->minimisedCollection($this->include_private);

        $conditional_post_fields = ['resource_type_id' => []];
        foreach ($resource_types as $resource_type) {
            $id = $this->hash->encode('resource_type', $resource_type['resource_type_id']);

            if ($id === false) {
                UtilityResponse::unableToDecode();
            }

            $conditional_post_fields['resource_type_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $resource_type['resource_type_name'],
                'description' => $resource_type['resource_type_description']
            ];
        }

        return $conditional_post_fields;
    }
}
