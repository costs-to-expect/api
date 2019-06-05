<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Get;
use App\Http\Parameters\Route\Validate;
use App\Models\Item;
use App\Models\Transformers\ItemCategorySummary as ItemCategorySummaryTransformer;
use App\Models\Transformers\ItemMonthSummary as ItemMonthSummaryTransformer;
use App\Models\Transformers\ItemSubCategorySummary as ItemSubCategorySummaryTransformer;
use App\Models\Transformers\ItemYearSummary as ItemYearSummaryTransformer;
use App\Utilities\General;
use App\Utilities\Response as UtilityResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Summary for the items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryItemController extends Controller
{
    private $resource_type_id;
    private $resource_id;

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
        Validate::resourceRoute($resource_type_id, $resource_id);

        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;

        $collection_parameters = Get::parameters([
            'year',
            'years',
            'month',
            'months',
            'category',
            'categories',
            'subcategory',
            'subcategories'
        ]);

        if (array_key_exists('years', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['years']) === true) {
            return $this->yearsSummary();
        } else if (array_key_exists('year', $collection_parameters) === true) {
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
        } else if (array_key_exists('categories', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['categories']) === true) {
            return $this->categoriesSummary();
        } else if (array_key_exists('category', $collection_parameters) === true) {
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
        } else {
            return $this->tcoSummary();
        }
    }

    /**
     * Return the total summary for a resource, total cost of ownership
     *
     * @return JsonResponse
     */
    private function tcoSummary(): JsonResponse
    {
        $summary = (new Item())->summary($this->resource_type_id, $this->resource_id);

        return response()->json(
            [
                'total' => number_format($summary[0]['actualised_total'], 2, '.', '')
            ],
            200,
            ['X-Total-Count' => 1]
        );
    }

    /**
     * Return the annualised summary for a resource
     *
     * @return JsonResponse
     */
    private function yearsSummary(): JsonResponse
    {
        $summary = (new Item())->yearsSummary($this->resource_type_id, $this->resource_id);

        return response()->json(
            $summary->map(
                function ($annual_summary) {
                    return (new ItemYearSummaryTransformer($annual_summary))->toArray();
                }
            ),
            200,
            ['X-Total-Count' => count($summary)]
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
        $summary = (new Item())->yearSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year
        );

        if (count($summary) !== 1) {
            UtilityResponse::notFound();
        }

        return response()->json(
            (new ItemYearSummaryTransformer($summary[0]))->toArray(),
            200,
            ['X-Total-Count' => 1]
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
        $summary = (new Item())->monthsSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year
        );

        return response()->json(
            $summary->map(
                function ($month_summary) {
                    return (new ItemMonthSummaryTransformer($month_summary))->toArray();
                }
            ),
            200,
            [ 'X-Total-Count' => count($summary) ]
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
        $summary = (new Item())->monthSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year,
            $month
        );

        if (count($summary) !== 1) {
            UtilityResponse::notFound();
        }

        return response()->json(
            (new ItemMonthSummaryTransformer($summary[0]))->toArray(),
            200,
            [ 'X-Total-Count' => 1 ]
        );
    }

    /**
     * Return the categories summary for a resource
     *
     * @return JsonResponse
     */
    public function categoriesSummary(): JsonResponse
    {
        $summary = (new Item())->categoriesSummary(
            $this->resource_type_id,
            $this->resource_id
        );

        return response()->json(
            array_map(
                function($category) {
                    return (new ItemCategorySummaryTransformer($category))->toArray();
                },
                $summary
            ),
            200,
            [ 'X-Total-Count' => count($summary) ]
        );
    }

    /**
     * Return the category summary for a resource
     *
     * @param integer $category_id
     *
     * @return JsonResponse
     */
    public function categorySummary(int $category_id): JsonResponse
    {
        Validate::categoryRoute($category_id);

        $summary = (new Item())->categorySummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id
        );

        if (count($summary) !== 1) {
            UtilityResponse::notFound();
        }

        return response()->json(
            (new ItemCategorySummaryTransformer($summary[0]))->toArray(),
            200,
            [ 'X-Total-Count' => 1 ]
        );
    }

    /**
     * Return the subcategories summary for a category
     *
     * @param integer $category_id
     *
     * @return JsonResponse
     */
    public function subcategoriesSummary(int $category_id): JsonResponse
    {
        Validate::categoryRoute($category_id);

        $summary = (new Item())->subCategoriesSummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id
        );

        return response()->json(
            array_map(
                function($subcategory) {
                    return (new ItemSubCategorySummaryTransformer($subcategory))->toArray();
                },
                $summary
            ),
            200,
            [ 'X-Total-Count' => count($summary) ]
        );
    }

    /**
     * Return the subcategories summary for a category
     *
     * @param integer $category_id
     * @param integer $sub_category_id
     *
     * @return JsonResponse
     */
    public function subcategorySummary(int $category_id, int $sub_category_id): JsonResponse
    {
        Validate::subCategoryRoute($category_id, $sub_category_id);

        $summary = (new Item())->subCategorySummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $sub_category_id
        );

        if (count($summary) !== 1) {
            UtilityResponse::notFound();
        }

        return response()->json(
            (new ItemSubCategorySummaryTransformer($summary[0]))->toArray(),
            200,
            [ 'X-Total-Count' => 1 ]
        );
    }

    /**
     * Generate the OPTIONS request for items route
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     */
    public function optionsIndex(Request $request, string $resource_type_id, string $resource_id)
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.summary_GET_resource-type_resource_items',
                'parameters_config' => 'api.item.summary-parameters.collection',
                'conditionals' => [],
                'sortable_config' => null,
                'pagination' => false,
                'authenticated' => false
            ]
        );
    }
}
