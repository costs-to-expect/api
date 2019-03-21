<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Expanded summaries
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ExpandedSummaryController extends Controller
{
    /**
     * Return the expanded sub categories summary, all sub categories and current counts and totals
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
    ): JsonResponse {

        Validate::resourceRoute($resource_type_id, $resource_id);

        $sub_categories_summary = (new SubCategory())->subCategorySummary(
            $resource_type_id,
            $resource_id
        );

        $headers = [
            'X-Total-Count' => count($sub_categories_summary)
        ];

        return response()->json(
            $sub_categories_summary,
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for expanded categories summary
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     */
    public function optionsCategories(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $routes = [
            'GET' => [
                'description' => config::get('api.route-descriptions.summary.GET_expanded_categories'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }
}
