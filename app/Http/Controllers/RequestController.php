<?php

namespace App\Http\Controllers;

use App\Models\RequestErrorLog;
use App\Models\RequestLog;
use App\Models\Transformers\RequestErrorLog as RequestErrorLogTransformer;
use App\Models\Transformers\RequestLog as RequestLogTransformer;
use App\Utilities\Pagination as UtilityPagination;
use App\Http\Parameters\Request\Validators\RequestErrorLog as RequestErrorLogValidator;
use App\Utilities\Response as UtilityResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestController extends Controller
{
    /**
     * Return the paginated request log
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function errorLog(Request $request): JsonResponse
    {
        $total = (new RequestErrorLog())->totalCount();

        $pagination = UtilityPagination::init($request->path(), $total, 50)
            ->paging();

        $log = (new RequestErrorLog())->paginatedCollection(
            $pagination['offset'],
            $pagination['limit']
        );

        $headers = [
            'X-Total-Count' => $total,
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next'],
        ];

        return response()->json(
            $log->map(
                function ($log_item)
                {
                    return (new RequestErrorLogTransformer($log_item))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Return the paginated request log
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function log(Request $request): JsonResponse
    {
        $total = (new RequestLog())->totalCount();

        $pagination = UtilityPagination::init($request->path(), $total, 50)
            ->paging();

        $log = (new RequestLog())->paginatedCollection(
            $pagination['offset'],
            $pagination['limit']
        );

        $headers = [
            'X-Total-Count' => $total,
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next'],
        ];

        return response()->json(
            $log->map(
                function ($log_item)
                {
                    return (new RequestLogTransformer($log_item))->toArray();
                }
            ),
            200,
            $headers
        );
    }

    /**
     * Return a summary of the monthly requests
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function monthlyRequests(Request $request): JsonResponse
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
     * Generate the OPTIONS request for log
     *
     * @param Request $request
     */
    public function optionsLog(Request $request)
    {
        $this->optionsResponse(
            [
                'GET' => [
                    'description' => config::get('api.route-descriptions.request.GET_log'),
                    'authenticated' => false,
                    'parameters' => []
                ]
            ]
        );
    }

    /**
     * Generate the OPTIONS request for error log
     *
     * @param Request $request
     */
    public function optionsErrorLog(Request $request)
    {
        return $this->generateOptionsForIndex(
            [
                'description_key' => 'api.route-descriptions.request.GET_error_log',
                'parameters_key' => 'api.routes.request.parameters.collection',
                'conditionals' => [],
                'authenticated' => false
            ],
            [
                'description_key' => 'api.route-descriptions.request.POST',
                'fields_key' => 'api.routes.request.fields',
                'conditionals' => [],
                'authenticated' => false
            ]
        );
    }

    /**
     * Generate the OPTIONS request for monthly request summary
     *
     * @param Request $request
     */
    public function optionsMonthlyRequests(Request $request)
    {
        $this->optionsResponse(
            [
                'GET' => [
                    'description' => config::get('api.route-descriptions.request.GET_log_monthly_requests'),
                    'authenticated' => false,
                    'parameters' => []
                ]
            ]
        );
    }

    /**
     * Log a request error, these are logged when the web app receives an unexpected
     * http status code response
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createErrorLog(Request $request): JsonResponse
    {
        $validator = (new RequestErrorLogValidator())->create($request);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $request_log = new RequestErrorLog([
                'method' => $request->input('method'),
                'expected_status_code' => $request->input('expected_status_code'),
                'returned_status_code' => $request->input('returned_status_code'),
                'request_uri' => $request->input('request_uri'),
            ]);
            $request_log->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return response()->json(
            [
                'message' => 'API request error log entry created'
            ],
            201
        );
    }
}
