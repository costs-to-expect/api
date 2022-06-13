<?php

namespace App\Http\Controllers;

use App\Models\RequestErrorLog;
use App\HttpOptionResponse\ErrorLog;
use App\Transformer\RequestErrorLog as RequestErrorLogTransformer;
use Illuminate\Http\JsonResponse;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestView extends Controller
{
    protected $collection_parameters = [];

    /**
     * Return the paginated request log
     *
     * @return JsonResponse
     */
    public function errorLog(): JsonResponse
    {
        $total = (new RequestErrorLog())->totalCount();

        $pagination = new \App\HttpResponse\Pagination(request()->path(), $total, 50);
        $pagination_parameters = $pagination->parameters();

        $logs = (new RequestErrorLog())->paginatedCollection(
            $pagination_parameters['offset'],
            $pagination_parameters['limit']
        );

        $headers = [
            'X-Count' => count($logs),
            'X-Total-Count' => $total,
            'X-Offset' => $pagination_parameters['offset'],
            'X-Limit' => $pagination_parameters['limit'],
            'X-Link-Previous' => $pagination_parameters['links']['previous'],
            'X-Link-Next' => $pagination_parameters['links']['next'],
        ];

        return response()->json(
            array_map(
                static function($log) {
                    return (new RequestErrorLogTransformer($log))->asArray();
                },
                $logs
            ),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for error log
     *
     * @return JsonResponse
     */
    public function optionsErrorLog(): JsonResponse
    {
        $response = new ErrorLog(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
