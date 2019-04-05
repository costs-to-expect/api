<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\Item;
use App\Models\Transformers\ItemMonthSummary as ItemMonthSummaryTransformer;
use App\Models\Transformers\ItemYearSummary as ItemYearSummaryTransformer;
use App\Utilities\Response as UtilityResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Resource summary
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryPeriodController extends Controller
{
    /**
     * Return the years summary for a resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function years(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $summary = (new Item())->yearsSummary(
            $resource_type_id,
            $resource_id
        );

        $headers = [
            'X-Total-Count' => count($summary)
        ];

        return response()->json(
            $summary->map(
                function ($year_summary) {
                    return (new ItemYearSummaryTransformer($year_summary))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the years summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     */
    public function optionsYears(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $routes = [
            'GET' => [
                'description' => trans('route-descriptions.summary_GET_years'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }

    /**
     * Return the months summary for a specific resource and year
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $year
     *
     * @return JsonResponse
     */
    public function year(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $year
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $year_summary = (new Item())->yearSummary(
            $resource_type_id,
            $resource_id,
            $year
        );

        if (count($year_summary) !== 1) {
            UtilityResponse::notFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemYearSummaryTransformer($year_summary[0]))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Return the months summary for a resource and year
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param integer $year
     *
     * @return JsonResponse
     */
    public function months(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        int $year
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $summary = (new Item())->monthsSummary(
            $resource_type_id,
            $resource_id,
            $year
        );

        $headers = [
            'X-Total-Count' => count($summary)
        ];

        return response()->json(
            $summary->map(
                function ($month_summary) {
                    return (new ItemMonthSummaryTransformer($month_summary))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the months summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     */
    public function optionsMonths(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        int $year
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $routes = [
            'GET' => [
                'description' => trans('route-descriptions.summary_GET_months'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }

    /**
     * Generate the OPTIONS request for a category summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $year
     */
    public function optionsYear(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $year
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $routes = [
            'GET' => [
                'description' => trans('route-descriptions.summary_GET_year'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }

    /**
     * Return the months summary for a resource and year
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param integer $year
     * @param integer $month
     *
     * @return JsonResponse
     */
    public function month(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        int $year,
        int $month
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $summary = (new Item())->monthSummary(
            $resource_type_id,
            $resource_id,
            $year,
            $month
        );

        if (count($summary) !== 1) {
            UtilityResponse::notFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemMonthSummaryTransformer($summary[0]))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for a year and month summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $year
     * @param string $month
     */
    public function optionsMonth(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $year,
        string $month
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $routes = [
            'GET' => [
                'description' => trans('route-descriptions.summary_GET_month'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }
}
