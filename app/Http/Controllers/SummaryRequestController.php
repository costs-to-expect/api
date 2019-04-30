<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryRequestController extends Controller
{
    /**
     * Return a summary of the access log, monthly
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function monthlyAccessLog(Request $request): JsonResponse
    {
        $monthly_summary = (new RequestLog())->monthlyRequests();

        $summary = [];
        foreach ($monthly_summary as $month) {
            $summary[$month['year']][] = ['month' => $month['month'], 'requests' => $month['requests']];
        }

        return response()->json(
            $summary,
            200
        );
    }

    /**
     * Generate the OPTIONS request for summary of the access log
     *
     * @param Request $request
     */
    public function optionsMonthlyAccessLog(Request $request)
    {
        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.summary_GET_request_access-log_monthly',
                'parameters_config' => [],
                'conditionals' => [],
                'sortable_config' => null,
                'pagination' => false,
                'authenticated' => false
            ]
        );
    }
}
