<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Utilities\Header;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\RoutePermission;
use App\Validators\Parameters;
use App\Validators\Route;
use App\Models\Category;
use App\Models\Transformers\Category as CategoryTransformer;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Fields\Category as CategoryValidator;
use App\Validators\SearchParameters;
use App\Validators\SortParameters;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryController extends Controller
{
    protected $allow_entire_collection = true;

    /**
     * Return the categories collection
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id): JsonResponse
    {
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $search_parameters = SearchParameters::fetch(
            Config::get('api.category.searchable')
        );

        $total = (new Category())->total(
            (int) $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public,
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
            (int) $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public,
            $pagination['offset'],
            $pagination['limit'],
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
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function show($resource_type_id, $category_id): JsonResponse
    {
        Route::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $parameters = Parameters::fetch(array_keys(Config::get('api.category.parameters.item')));

        $category = (new Category)->single(
            (int) $resource_type_id,
            (int) $category_id
        );

        if ($category === null) {
            UtilityResponse::notFound(trans('entities.category'));
        }

        $subcategories = [];
        if (
            array_key_exists('include-subcategories', $parameters) === true &&
            $parameters['include-subcategories'] === true
        ) {
            $subcategories = (new Subcategory())->paginatedCollection(
                (int) $resource_type_id,
                (int) $category_id,
                0,
                100
            );
        }

        $headers = new Header();
        $headers->item();

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new CategoryTransformer($category, $subcategories))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the category list
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id): JsonResponse
    {
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.category.parameters.collection')->
            setSortable('api.category.sortable')->
            setSearchable('api.category.searchable')->
            setPaginationOverride(true)->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.category_GET_index')->
            option();

        $post = Post::init()->
            setFields('api.category.fields')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
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
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function optionsShow($resource_type_id, $category_id): JsonResponse
    {
        Route::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.category.parameters.item')->
            setDescription('route-descriptions.category_GET_show')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $delete = Delete::init()->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            setDescription('route-descriptions.category_DELETE')->
            option();

        $patch = Patch::init()->
            setFields('api.category.fields-patch')->
            setDescription('route-descriptions.category_PATCH')->
            setAuthenticationStatus($permissions['manage'])->
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
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function create($resource_type_id): JsonResponse
    {
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $validator = (new CategoryValidator)->create([
            'resource_type_id' => $resource_type_id
        ]);
        UtilityRequest::validateAndReturnErrors($validator);

        //try {
            $category = new Category([
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'resource_type_id' => $resource_type_id
            ]);
            $category->save();
        //} catch (Exception $e) {
          //  UtilityResponse::failedToSaveModelForCreate();
        //}

        return response()->json(
            (new CategoryTransformer((new Category)->instanceToArray($category)))->toArray(),
            201
        );
    }

    /**
     * Delete the requested category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function delete(
        $resource_type_id,
        $category_id
    ): JsonResponse
    {
        Route::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types,
            true
        );

        try {
            (new Category())->find($category_id)->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.category'), $e);
        }
    }

    /**
     * Update the selected category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function update($resource_type_id, $category_id): JsonResponse
    {
        Route::category(
            (int) $resource_type_id,
            (int) $category_id,
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
