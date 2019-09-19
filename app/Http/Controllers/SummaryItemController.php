<?php

namespace App\Http\Controllers;

use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\Response;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\ItemSummary;
use App\Models\Transformers\ItemCategorySummary as ItemCategorySummaryTransformer;
use App\Models\Transformers\ItemMonthSummary as ItemMonthSummaryTransformer;
use App\Models\Transformers\ItemSubCategorySummary as ItemSubCategorySummaryTransformer;
use App\Models\Transformers\ItemYearSummary as ItemYearSummaryTransformer;
use App\Utilities\General;
use App\Validators\Request\SearchParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Summary for the items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryItemController extends Controller
{
    private $resource_type_id;
    private $resource_id;
    private $include_unpublished = false;

    /**
     * Return the TCO for the resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        Route::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;

        $collection_parameters = Parameters::fetch([
            'include-unpublished',
            'year',
            'years',
            'month',
            'months',
            'category',
            'categories',
            'subcategory',
            'subcategories'
        ]);

        $search_parameters = SearchParameters::fetch([
            'description'
        ]);

        if (
            array_key_exists('include-unpublished', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['include-unpublished']) === true
        ) {
            $this->include_unpublished = true;
        }

        if (array_key_exists('years', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['years']) === true) {
            return $this->yearsSummary();
        } else if (
            array_key_exists('year', $collection_parameters) === true &&
            array_key_exists('category', $collection_parameters) === false &&
            array_key_exists('subcategory', $collection_parameters) === false &&
            count($search_parameters) === 0
        ) {
            if (array_key_exists('months', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['months']) === true) {
                return $this->monthsSummary($collection_parameters['year']);
            } else if (array_key_exists('month', $collection_parameters) === true) {
                return $this->monthSummary(
                    $collection_parameters['year'],
                    $collection_parameters['month']
                );
            } else {
                return $this->yearSummary($collection_parameters['year']);
            }
        }

        if (array_key_exists('categories', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['categories']) === true) {
            return $this->categoriesSummary();
        } else if (
            array_key_exists('category', $collection_parameters) === true &&
            array_key_exists('year', $collection_parameters) === false &&
            array_key_exists('month', $collection_parameters) === false &&
            count($search_parameters) === 0
        ) {
            if (array_key_exists('subcategories', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['subcategories']) === true) {
                return $this->subcategoriesSummary($collection_parameters['category']);
            } else if (array_key_exists('subcategory', $collection_parameters) === true) {
                return $this->subcategorySummary(
                    $collection_parameters['category'],
                    $collection_parameters['subcategory']
                );
            } else {
                return $this->categorySummary($collection_parameters['category']);
            }
        }

        if (
            array_key_exists('category', $collection_parameters) === true ||
            array_key_exists('subcategory', $collection_parameters) === true ||
            array_key_exists('year', $collection_parameters) === true ||
            array_key_exists('month', $collection_parameters) === true ||
            count($search_parameters) > 0
        ) {
            return $this->filteredSummary(
                (array_key_exists('category', $collection_parameters) ? $collection_parameters['category'] : null),
                (array_key_exists('subcategory', $collection_parameters) ? $collection_parameters['subcategory'] : null),
                (array_key_exists('year', $collection_parameters) ? $collection_parameters['year'] : null),
                (array_key_exists('month', $collection_parameters) ? $collection_parameters['month'] : null),
                (count($search_parameters) > 0 ? $search_parameters : [])
            );
        }

        return $this->tcoSummary();
    }

    /**
     * Return the total summary for a resource, total cost of ownership
     *
     * @return JsonResponse
     */
    private function tcoSummary(): JsonResponse
    {
        $summary = (new ItemSummary())->summary(
            $this->resource_type_id,
            $this->resource_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', 1);
        $headers->add('X-Count', 1);

        return response()->json(
            [
                'total' => number_format($summary[0]['actualised_total'], 2, '.', '')
            ],
            200,
            $headers->headers()
        );
    }

    /**
     * Return the annualised summary for a resource
     *
     * @return JsonResponse
     */
    private function yearsSummary(): JsonResponse
    {
        $summary = (new ItemSummary())->yearsSummary(
            $this->resource_type_id,
            $this->resource_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            array_map(
                function($year) {
                    return (new ItemYearSummaryTransformer($year))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total cost for a specific year
     *
     * @param integer $year
     *
     * @return JsonResponse
     */
    private function yearSummary(int $year): JsonResponse
    {
        $summary = (new ItemSummary())->yearSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            (new ItemYearSummaryTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the monthly summary for a specific year
     *
     * @param integer $year
     *
     * @return JsonResponse
     */
    private function monthsSummary(int $year): JsonResponse
    {
        $summary = (new ItemSummary())->monthsSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            array_map(
                function($month) {
                    return (new ItemMonthSummaryTransformer($month))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the month summary for a specific year and month
     *
     * @param integer $year
     * @param integer $month
     *
     * @return JsonResponse
     */
    private function monthSummary(int $year, int $month): JsonResponse
    {
        $summary = (new ItemSummary())->monthSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year,
            $month,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            (new ItemMonthSummaryTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the categories summary for a resource
     *
     * @return JsonResponse
     */
    private function categoriesSummary(): JsonResponse
    {
        $summary = (new ItemSummary())->categoriesSummary(
            $this->resource_type_id,
            $this->resource_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            array_map(
                function($category) {
                    return (new ItemCategorySummaryTransformer($category))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return a filtered summary
     *
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $search_parameters
     *
     * @return JsonResponse
     */
    private function filteredSummary(
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $search_parameters = []
    ): JsonResponse
    {
        $summary = (new ItemSummary())->filteredSummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $subcategory_id,
            $year,
            $month,
            $search_parameters,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            [
                'total' => number_format($summary[0]['total'], 2, '.', '')
            ],
            200,
            $headers->headers()
        );
    }

    /**
     * Return the category summary for a resource
     *
     * @param integer $category_id
     *
     * @return JsonResponse
     */
    private function categorySummary(int $category_id): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types
        );

        $summary = (new ItemSummary())->categorySummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            (new ItemCategorySummaryTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the subcategories summary for a category
     *
     * @param integer $category_id
     *
     * @return JsonResponse
     */
    private function subcategoriesSummary(int $category_id): JsonResponse
    {
        Route::category(
            $category_id,
            $this->permitted_resource_types
        );

        $summary = (new ItemSummary())->subCategoriesSummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            array_map(
                function($subcategory) {
                    return (new ItemSubCategorySummaryTransformer($subcategory))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the subcategories summary for a category
     *
     * @param integer $category_id
     * @param integer $subcategory_id
     *
     * @return JsonResponse
     */
    private function subcategorySummary(int $category_id, int $subcategory_id): JsonResponse
    {
        Route::subcategory(
            $category_id,
            $subcategory_id,
            $this->permitted_resource_types
        );

        $summary = (new ItemSummary())->subCategorySummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $subcategory_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        return response()->json(
            (new ItemSubCategorySummaryTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for items route
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id, string $resource_id)
    {
        Route::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.item.summary-parameters.collection')->
            setSearchable('api.item.searchable')->
            setDescription('route-descriptions.summary_GET_resource-type_resource_items')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        return $this->optionsResponse($get, 200);
    }
}
