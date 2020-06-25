<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Option\Get;
use App\Response\Cache;
use App\Request\Parameter;
use App\Models\Summary\RequestLog;
use App\Response\Header\Headers;
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
    private $collection_parameters;

    /**
     * Return a summary of the access log, requests per year and month
     *
     * @return JsonResponse
     */
    public function accessLog(): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneHour();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $this->collection_parameters = Parameter\Request::fetch(
                array_keys(Config::get('api.request-access-log.summary-parameters'))
            );

            $request_data = (new RequestLog())->monthlyRequests($this->collection_parameters);

            $collection = [];
            foreach ($request_data as $month) {
                $collection[$month['year']][] = ['month' => $month['month'], 'requests' => $month['requests']];
            }

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader())->
                addSearch(Parameter\Search::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    /**
     * Generate the OPTIONS request for summary of the access log
     *
     * @return JsonResponse
     */
    public function optionsAccessLog(): JsonResponse
    {
        $get = Get::init()->
            setParameters('api.request-access-log.parameters.collection')->
            setDescription('route-descriptions.summary_GET_request_access-log')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        return $this->optionsResponse($get, 200);
    }
}
