<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Utilities\Header;
use App\Utilities\Pagination as UtilityPagination;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\Category;
use App\Models\ResourceType;
use App\Models\Transformers\Category as CategoryTransformer;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\Category as CategoryValidator;
use App\Validators\Request\SearchParameters;
use App\Validators\Request\SortParameters;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryController extends Controller
{
    protected $allow_entire_collection = true;

    /**
     * Return the categories collection
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $search_parameters = SearchParameters::fetch(
            Config::get('api.category.searchable')
        );

        $total = (new Category())->totalCount(
            $this->permitted_resource_types,
            $this->include_public,
            [],
            $search_parameters
        );

        $sort_parameters = SortParameters::fetch(
            Config::get('api.category.sortable')
        );

        $pagination = UtilityPagination::init(
                request()->path(),
                $total,
                10,
                $this->allow_entire_collection
            )->
            setSearchParameters($search_parameters)->
            setSortParameters($sort_parameters)->
            paging();

        $categories = (new Category())->paginatedCollection(
            $this->permitted_resource_types,
            $this->include_public,
            $pagination['offset'],
            $pagination['limit'],
            [],
            $search_parameters,
            $sort_parameters
        );

        $headers = new Header();
        $headers->collection($pagination, count($categories), $total);

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers->addSort($sort_header);
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers->addSearch($search_header);
        }

        return response()->json(
            array_map(
                function($category) {
                    return (new CategoryTransformer($category))->toArray();
                },
                $categories
            ),
            200,
            $headers->headers()
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
        Route::category(
            $category_id,
            $this->permitted_resource_types
        );

        $parameters = Parameters::fetch(['include-subcategories']);

        $category = (new Category)->single($category_id);

        if ($category === null) {
            UtilityResponse::notFound(trans('entities.category'));
        }

        $subcategories = [];
        if (
            array_key_exists('include-subcategories', $parameters) === true &&
            $parameters['include-subcategories'] === true
        ) {
            $subcategories = (new SubCategory())->paginatedCollection(
                $category_id,
                0,
                100
            );
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new CategoryTransformer($category, $subcategories))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the category list
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $get = Get::init()->
            setParameters('api.category.parameters.collection')->
            setSortable('api.category.sortable')->
            setSearchable('api.category.searchable')->
            setPaginationOverride(true)->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            setDescription('route-descriptions.category_GET_index')->
            option();

        $post = Post::init()->
            setFields('api.category.fields')->
            setConditionalFields($this->conditionalPostParameters())->
            setAuthenticationRequired(true)->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            setDescription('route-descriptions.category_POST')->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request for a specific category
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function optionsShow(string $category_id): JsonResponse
    {
        $authenticated = Route::category(
            $category_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.category.parameters.item')->
            setAuthenticationStatus($authenticated)->
            setDescription('route-descriptions.category_GET_show')->
            option();

        $delete = Delete::init()->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($authenticated)->
            setDescription('route-descriptions.category_DELETE')->
            option();

        $patch = Patch::init()->
            setFields('api.category.fields-patch')->
            setDescription('route-descriptions.category_PATCH')->
            setAuthenticationStatus($authenticated)->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }

    /**
     * Create a new category
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $validator = (new CategoryValidator)->create();
        UtilityRequest::validateAndReturnErrors($validator);

        try {
            $resource_type_id = $this->hash->decode('resource_type', request()->input('resource_type_id'));

            if ($resource_type_id === false) {
                UtilityResponse::unableToDecode();
            }

            $category = new Category([
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'resource_type_id' => $resource_type_id
            ]);
            $category->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new CategoryTransformer((new Category)->instanceToArray($category)))->toArray(),
            201
        );
    }

    /**
     * Delete the requested category
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $category_id
    ): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types,
            true
        );

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
        $resource_types = (new ResourceType())->minimisedCollection(
            $this->permitted_resource_types,
            $this->include_public
        );

        $conditional_post_fields = ['resource_type_id' => []];
        foreach ($resource_types as $resource_type) {
            $id = $this->hash->encode('resource_type', $resource_type['resource_type_id']);

            if ($id === false) {
                UtilityResponse::unableToDecode();
            }

            $conditional_post_fields['resource_type_id']['allowed-values'][$id] = [
                'value' => $id,
                'name' => $resource_type['resource_type_name'],
                'description' => $resource_type['resource_type_description']
            ];
        }

        return $conditional_post_fields;
    }

    /**
     * Update the selected category
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function update(
        string $category_id
    ): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types,
            true
        );

        $category = (new Category())->instance($category_id);

        if ($category === null) {
            UtilityResponse::failedToSelectModelForUpdate();
        }

        UtilityRequest::checkForEmptyPatch();

        $validator = (new CategoryValidator)->update([
            'resource_type_id' => intval($category->resource_type_id),
            'category_id' => intval($category_id)
        ]);
        UtilityRequest::validateAndReturnErrors($validator);

        UtilityRequest::checkForInvalidFields(
            array_merge(
                (new Category())->patchableFields(),
                (new CategoryValidator)->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $category->$key = $value;
        }

        try {
            $category->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForUpdate();
        }

        UtilityResponse::successNoContent();
    }
}
