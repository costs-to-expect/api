<?php

namespace App\Http\Controllers;

use App\Events\RequestError;
use App\Option\Get;
use App\Option\Post;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Models\RequestErrorLog;
use App\Models\RequestLog;
use App\Models\Transformers\RequestErrorLog as RequestErrorLogTransformer;
use App\Models\Transformers\RequestLog as RequestLogTransformer;
use App\Response\Pagination as UtilityPagination;
use App\Request\Validate\RequestErrorLog as RequestErrorLogValidator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestController extends Controller
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
    public function optionsAccessLog()
    {
        $get = Get::init()->
            setParameters('api.request-access-log.parameters.collection')->
            setPagination(true)->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            setDescription('route-descriptions.request_GET_access-log')->
            option();

        return $this->optionsResponse($get, 200);
    }

    /**
     * Generate the OPTIONS request for error log
     *
     * @return JsonResponse
     */
    public function optionsErrorLog()
    {
        $get = Get::init()->
            setDescription('route-descriptions.request_GET_error_log')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        $post = Post::init()->
            setFields('api.request-error-log.fields')->
            setDescription('route-descriptions.request_POST')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Log a request error, these are logged when the web app receives an unexpected
     * http status code response
     *
     * @return JsonResponse
     */
    public function createErrorLog(): JsonResponse
    {
        $validator = (new RequestErrorLogValidator())->create();
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        try {
            $request_error_log = new RequestErrorLog([
                'method' => request()->input('method'),
                'source' => request()->input('source'),
                'expected_status_code' => request()->input('expected_status_code'),
                'returned_status_code' => request()->input('returned_status_code'),
                'request_uri' => request()->input('request_uri'),
                'debug' => request()->input('debug')
            ]);
            $request_error_log->save();

            event(new RequestError([
                'method' => request()->input('method'),
                'source' => request()->input('source'),
                'expected_status_code' => request()->input('expected_status_code'),
                'returned_status_code' => request()->input('returned_status_code'),
                'request_uri' => request()->input('request_uri'),
                'referer' => request()->server('HTTP_REFERER', 'NOT SET!'),
                'debug' => request()->input('debug')
            ]));

        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForCreate();
        }

        return \App\Response\Responses::successNoContent();
    }
}
