<?php

namespace App\Http\Controllers;

use App\Utilities\Pagination as UtilityPagination;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubcategoryController extends Controller
{
    /**
     * Return all the sub categories assigned to the given category
     *
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function index(string $category_id): JsonResponse
    {
        Route::categoryRoute($category_id);

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

        $pagination = UtilityPagination::init(request()->path(), $total)->
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
        Route::subCategoryRoute($category_id, $sub_category_id);

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
                'X-Total-Count' => 1
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
        Route::categoryRoute($category_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation_string' => 'route-descriptions.sub_category_GET_index',
                'parameters_config_string' => 'api.subcategory.parameters.collection',
                'conditionals_config' => [],
                'sortable_config' => 'api.subcategory.sortable',
                'searchable_config' => 'api.subcategory.searchable',
                'enable_pagination' => true,
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.sub_category_POST',
                'fields_config' => 'api.subcategory.fields',
                'conditionals_config' => [],
                'authentication_required' => true
            ]
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
        Route::subCategoryRoute($category_id, $sub_category_id);

        return $this->generateOptionsForShow(
            [
                'description_localisation_string' => 'route-descriptions.sub_category_GET_show',
                'parameters_config_string' => 'api.subcategory.parameters.item',
                'conditionals_config' => [],
                'authentication_required' => false
            ],
            [
                'description_localisation_string' => 'route-descriptions.sub_category_DELETE',
                'authentication_required' => true
            ]
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
        Route::categoryRoute($category_id);

        $validator = (new SubCategoryValidator)->create(['category_id' => $category_id]);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $sub_category = new SubCategory([
                'category_id' => $category_id,
                'name' => $request->input('name'),
                'description' => $request->input('description')
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
        Route::subCategoryRoute($category_id, $sub_category_id);

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
}
