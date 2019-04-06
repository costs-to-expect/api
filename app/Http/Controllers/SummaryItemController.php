<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Get;
use App\Http\Parameters\Route\Validate;
use App\Models\Item;
use App\Models\Transformers\ItemMonthSummary as ItemMonthSummaryTransformer;
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
        } else {
            if (array_key_exists('year', $collection_parameters) === true) {

                if (array_key_exists('months', $collection_parameters) === true &&
                    General::booleanValue($collection_parameters['months']) === true) {
                    return $this->monthsSummary($collection_parameters['year']);
                } else {
                    return $this->yearSummary($collection_parameters['year']);
                }
            } else {
                return $this->tcoSummary();
            }
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
                'pagination' => false,
                'authenticated' => false
            ]
        );
    }
}
