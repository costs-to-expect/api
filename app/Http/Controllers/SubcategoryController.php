<?php

namespace App\Http\Controllers;

use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Request as UtilityRequest;
use App\Validators\Request\Route;
use App\Models\SubCategory;
use App\Models\Transformers\SubCategory as SubCategoryTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\SubCategory as SubCategoryValidator;
use App\Validators\Request\SearchParameters;
use App\Validators\Request\SortParameters;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubcategoryController extends Controller
{
    protected $allow_entire_collection = true;

    /**
     * Return all the sub categories assigned to the given category
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function index(string $category_id): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types
        );

        $search_parameters = SearchParameters::fetch(
            Config::get('api.subcategory.searchable')
        );

        $total = (new SubCategory())->totalCount(
            $category_id,
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

        $subcategories = (new SubCategory())->paginatedCollection(
            $category_id,
            $pagination['offset'],
            $pagination['limit'],
            $search_parameters,
            $sort_parameters
        );

        $headers = [
            'X-Count' => count($subcategories),
            'X-Total-Count' => $total,
            'X-Offset' => $pagination['offset'],
            'X-Limit' => $pagination['limit'],
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next']
        ];

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers['X-Sort'] = $sort_header;
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers['X-Search'] = $search_header;
        }

        return response()->json(
            array_map(
                function($subcategory) {
                    return (new SubCategoryTransformer($subcategory))->toArray();
                },
                $subcategories
            ),
            200,
            $headers
        );
    }

    /**
     * Return a single sub category
     *
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function show(
        string $category_id,
        string $sub_category_id
    ): JsonResponse
    {
        Route::subcategory($category_id, $sub_category_id);

        $subcategory = (new SubCategory())->single(
            $category_id,
            $sub_category_id
        );

        if ($subcategory === null) {
            UtilityResponse::notFound();
        }

        return response()->json(
            (new SubCategoryTransformer($subcategory))->toArray(),
            200,
            [
                'X-Total-Count' => 1,
                'X-Count' => 1
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories list
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $category_id): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setDescription('route-descriptions.sub_category_GET_index')->
            setSortable('api.subcategory.sortable')->
            setSearchable('api.subcategory.searchable')->
            setPaginationOverride(true)->
            setParameters('api.subcategory.parameters.collection')->
            option();

        $post = Post::init()->
            setDescription('route-descriptions.sub_category_POST')->
            setFields('api.subcategory.fields')->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request for the specific sub category
     *
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        string $category_id,
        string $sub_category_id
    ): JsonResponse
    {
        Route::subcategory($category_id, $sub_category_id);

        $get = Get::init()->
            setDescription('route-descriptions.sub_category_GET_show')->
            setParameters('api.subcategory.parameters.item')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.sub_category_DELETE')->
            setAuthenticationRequired(true)->
            option();

        $patch = Patch::init()->
            setDescription('route-descriptions.sub_category_PATCH')->
            setFields('api.subcategory.fields')->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }

    /**
     * Create a new sub category
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function create(string $category_id): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types,
            false
        );

        $validator = (new SubCategoryValidator)->create(['category_id' => $category_id]);
        UtilityRequest::validateAndReturnErrors($validator);

        try {
            $sub_category = new SubCategory([
                'category_id' => $category_id,
                'name' => request()->input('name'),
                'description' => request()->input('description')
            ]);
            $sub_category->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            (new SubCategoryTransformer((new SubCategory())->instanceToArray($sub_category)))->toArray(),
            201
        );
    }

    /**
     * Delete the requested sub category
     *
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $category_id,
        string $sub_category_id
    ): JsonResponse
    {
        Route::subcategory($category_id, $sub_category_id);

        $sub_category = (new SubCategory())->single(
            $category_id,
            $sub_category_id
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
            UtilityResponse::notFound(trans('entities.subcategory'));
        }
    }

    /**
     * Update the selected subcategory
     *
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function update(
        string $category_id,
        string $sub_category_id
    ): JsonResponse
    {
        Route::subcategory($category_id, $sub_category_id);

        $subcategory = (new SubCategory())->instance($category_id, $sub_category_id);

        if ($subcategory === null) {
            UtilityResponse::failedToSelectModelForUpdate();
        }

        UtilityRequest::checkForEmptyPatch();

        $validator = (new SubCategoryValidator())->update([
            'category_id' => intval($category_id),
            'subcategory_id' => intval($sub_category_id)
        ]);
        UtilityRequest::validateAndReturnErrors($validator);

        UtilityRequest::checkForInvalidFields(
            array_merge(
                (new SubCategory())->patchableFields(),
                (new SubCategoryValidator)->dynamicDefinedFields()
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
