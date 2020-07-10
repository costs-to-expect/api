<?php

namespace App\Http\Controllers;

use App\Option\AccessLog;
use App\Option\ErrorLog;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Models\RequestErrorLog;
use App\Models\RequestLog;
use App\Models\Transformers\RequestErrorLog as RequestErrorLogTransformer;
use App\Models\Transformers\RequestLog as RequestLogTransformer;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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

        $pagination = new UtilityPagination(request()->path(), $total, 50);
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
     * Return the paginated access log
     *
     * @return JsonResponse
     */
    public function accessLog(): JsonResponse
    {
        $total = (new RequestLog())->totalCount();

        $this->collection_parameters = Parameter\Request::fetch(
            array_keys(Config::get('api.request-access-log.parameters.collection'))
        );

        $pagination = new UtilityPagination(request()->path(), $total, 25);
        $pagination_parameters = $pagination->setParameters($this->collection_parameters)->
            parameters();

        $log = (new RequestLog())->paginatedCollection(
            $pagination_parameters['offset'],
            $pagination_parameters['limit'],
            $this->collection_parameters
        );

        $headers = new Header();
        $headers->collection($pagination_parameters, count($log), $total);

        $parameters_header = Parameter\Request::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        $json = [];
        foreach ($log as $log_item) {
            $json[] = (new RequestLogTransformer($log_item))->asArray();
        }

        return response()->json($json, 200, $headers->headers());
    }

    /**
     * Generate the OPTIONS request for log
     *
     * @return JsonResponse
     */
    public function optionsAccessLog(): JsonResponse
    {
        $response = new AccessLog(['view'=> $this->user_id !== null]);

        return $response->create()->response();
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
