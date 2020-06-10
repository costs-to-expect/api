<?php

namespace App\Http\Controllers;

use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Response\Header\Header;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\RoutePermission;
use App\Validators\Validate;
use App\Models\Subcategory;
use App\Models\Transformers\Subcategory as SubcategoryTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Fields\Subcategory as SubcategoryValidator;
use App\Validators\SearchParameters;
use App\Validators\SortParameters;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubcategoryController extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the sub categories assigned to the given category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id, $category_id): JsonResponse
    {
        \App\Request\Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $search_parameters = SearchParameters::fetch(
            array_keys(Config::get('api.subcategory.searchable'))
        );

        $total = (new Subcategory())->totalCount(
            (int) $resource_type_id,
            (int) $category_id,
            $search_parameters
        );

        $sort_parameters = SortParameters::fetch(
            Config::get('api.subcategory.sortable')
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

        $subcategories = (new Subcategory())->paginatedCollection(
            (int) $resource_type_id,
            (int) $category_id,
            $pagination['offset'],
            $pagination['limit'],
            $search_parameters,
            $sort_parameters
        );

        $headers = new Header();
        $headers->collection($pagination, count($subcategories), $total);

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
                function($subcategory) {
                    return (new SubcategoryTransformer($subcategory))->toArray();
                },
                $subcategories
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return a single sub category
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function show(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse
    {
        \App\Request\Route\Validate::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types
        );

        $subcategory = (new Subcategory())->single(
            $category_id,
            $subcategory_id
        );

        if ($subcategory === null) {
            UtilityResponse::notFound();
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new SubcategoryTransformer($subcategory))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories list
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id, $category_id): JsonResponse
    {
        \App\Request\Route\Validate::category(
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
            setSortable('api.subcategory.sortable')->
            setSearchable('api.subcategory.searchable')->
            setPaginationOverride(true)->
            setParameters('api.subcategory.parameters.collection')->
            setDescription('route-descriptions.sub_category_GET_index')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $post = Post::init()->
            setFields('api.subcategory.fields')->
            setDescription('route-descriptions.sub_category_POST')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request for the specific sub category
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse
    {
        \App\Request\Route\Validate::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.subcategory.parameters.item')->
            setDescription('route-descriptions.sub_category_GET_show')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.sub_category_DELETE')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        $patch = Patch::init()->
            setFields('api.subcategory.fields')->
            setDescription('route-descriptions.sub_category_PATCH')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }

    /**
     * Create a new sub category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function create($resource_type_id, $category_id): JsonResponse
    {
        \App\Request\Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types,
            true
        );

        $validator = (new SubcategoryValidator)->create(['category_id' => $category_id]);
        UtilityRequest::validateAndReturnErrors($validator);

        try {
            $sub_category = new Subcategory([
                'category_id' => $category_id,
                'name' => request()->input('name'),
                'description' => request()->input('description')
            ]);
            $sub_category->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new SubcategoryTransformer((new Subcategory())->instanceToArray($sub_category)))->toArray(),
            201
        );
    }

    /**
     * Delete the requested sub category
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function delete(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse
    {
        \App\Request\Route\Validate::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types,
            true
        );

        $sub_category = (new Subcategory())->instance(
            $category_id,
            $subcategory_id
        );

        if ($sub_category === null) {
            UtilityResponse::notFound(trans('entities.subcategory'));
        }

        try {
            $sub_category->delete();

            UtilityResponse::successNoContent();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::notFound(trans('entities.subcategory'), $e);
        }
    }

    /**
     * Update the selected subcategory
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function update(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse
    {
        \App\Request\Route\Validate::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types,
            true
        );

        $subcategory = (new Subcategory())->instance($category_id, $subcategory_id);

        if ($subcategory === null) {
            UtilityResponse::failedToSelectModelForUpdateOrDelete();
        }

        UtilityRequest::checkForEmptyPatch();

        $validator = (new SubcategoryValidator())->update([
            'category_id' => intval($category_id),
            'subcategory_id' => intval($subcategory_id)
        ]);
        UtilityRequest::validateAndReturnErrors($validator);

        UtilityRequest::checkForInvalidFields(
            array_merge(
                (new Subcategory())->patchableFields(),
                (new SubcategoryValidator)->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $subcategory->$key = $value;
        }

        try {
            $subcategory->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForUpdate();
        }

        UtilityResponse::successNoContent();
    }
}
