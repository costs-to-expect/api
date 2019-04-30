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
     * Return the paginated access log
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function accessLog(Request $request): JsonResponse
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
     * Generate the OPTIONS request for log
     *
     * @param Request $request
     */
    public function optionsAccessLog(Request $request)
    {
        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.request_GET_access-log',
                'parameters_config' => [],
                'conditionals' => [],
                'sortable_config' => null,
                'pagination' => true,
                'authenticated' => false
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
                'description_localisation' => 'route-descriptions.request_GET_error_log',
                'parameters_config' => 'api.request.parameters.collection',
                'conditionals' => [],
                'sortable_config' => null,
                'pagination' => false,
                'authenticated' => false
            ],
            [
                'description_localisation' => 'route-descriptions.request_POST',
                'fields_config' => 'api.request.fields',
                'conditionals' => [],
                'authenticated' => false
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
