<?php

namespace App\Http\Controllers;

use App\Models\RequestErrorLog;
use App\Models\RequestLog;
use App\Transformers\RequestErrorLog as RequestErrorLogTransformer;
use App\Transformers\RequestLog as RequestLogTransformer;
use App\Utilities\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
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
}
