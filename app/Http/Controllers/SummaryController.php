<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\Item;
use App\Models\Transformers\ItemCategorySummary as ItemCategorySummaryTransformer;
use App\Models\Transformers\ItemSubCategorySummary as ItemSubCategorySummaryTransformer;
use App\Models\Transformers\ItemMonthSummary as ItemMonthSummaryTransformer;
use App\Models\Transformers\ItemYearSummary as ItemYearSummaryTransformer;
use App\Utilities\Response as UtilityResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Resource summary
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryController extends Controller
{
    /**
     * Return the TCO for the resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function tco(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $summary = (new Item())->summary($resource_type_id, $resource_id);

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            [
                'total' => number_format($summary, 2, '.', '')
            ],
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the TCO
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     */
    public function optionsTco(Request $request, string $resource_type_id, string $resource_id)
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_tco'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }

    /**
     * Return the categories summary for a resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function categories(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $summary = (new Item())->categoriesSummary(
            $resource_type_id,
            $resource_id
        );

        $headers = [
            'X-Total-Count' => count($summary)
        ];

        return response()->json(
            $summary->map(
                function ($category_summary) {
                    return (new ItemCategorySummaryTransformer($category_summary))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the categories summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     */
    public function optionsCategories(Request $request, string $resource_type_id, string $resource_id)
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_categories'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }

    /**
     * Return the resource category summary for a specific category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function category(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::categoryRoute($category_id);

        $category_summary = (new Item())->categorySummary(
            $resource_type_id,
            $resource_id,
            $category_id
        );

        if (count($category_summary) !== 1) {
            UtilityResponse::notFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemCategorySummaryTransformer($category_summary[0]))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for a category summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     */
    public function optionsCategory(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::categoryRoute($category_id);

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_category'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }

    /**
     * Return the sub categories summary for a resource and category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     *
     * @return JsonResponse
     */
    public function subCategories(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::categoryRoute($category_id);

        $summary = (new Item())->subCategoriesSummary(
            $resource_type_id,
            $resource_id,
            $category_id
        );

        $headers = [
            'X-Total-Count' => count($summary)
        ];

        return response()->json(
            $summary->map(
                function ($category_summary) {
                    return (new ItemSubCategorySummaryTransformer($category_summary))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     */
    public function optionsSubCategories(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::categoryRoute($category_id);

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_sub_categories'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }

    /**
     * Return the resource sub category summary for a specific sub category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     * @param string $sub_category_id
     *
     * @return JsonResponse
     */
    public function subCategory(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id,
        string $sub_category_id
    ): JsonResponse
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::subCategoryRoute($category_id, $sub_category_id);

        $sub_category_summary = (new Item())->subCategorySummary(
            $resource_type_id,
            $resource_id,
            $category_id,
            $sub_category_id
        );

        if (count($sub_category_summary) !== 1) {
            UtilityResponse::notFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemSubCategorySummaryTransformer($sub_category_summary[0]))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the sub category summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $category_id
     * @param string $sub_category_id
     */
    public function optionsSubCategory(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $category_id,
        string $sub_category_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        Validate::subCategoryRoute($category_id, $sub_category_id);

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_sub_category'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }

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
                'description' => Config::get('api.descriptions.summary.GET_years'),
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
                'description' => Config::get('api.descriptions.summary.GET_months'),
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
                'description' => Config::get('api.descriptions.summary.GET_year'),
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
                'description' => Config::get('api.descriptions.summary.GET_month'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }
}
