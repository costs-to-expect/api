<?php

namespace App\Http\Controllers;

use App\Models\RequestErrorLog;
use App\Transformers\RequestErrorLog as RequestErrorLogTransformer;
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

        $log = (new RequestErrorLog())->paginatedCollection(0, 50);

        $headers = [
            'X-Total-Count' => $total
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
}
