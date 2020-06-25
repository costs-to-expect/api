<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Models\Summary\ResourceType;
use App\Option\Get;
use App\Response\Cache;
use App\Request\Parameter;
use App\Response\Header\Headers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the resource-type routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    /**
     * Return a summary of the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                array_keys(Config::get('api.resource-type.summary-searchable'))
            );

            $summary = (new ResourceType())->totalCount(
                $this->permitted_resource_types,
                $this->include_public,
                $search_parameters
            );

            $collection = [
                'resource_types' => $summary
            ];

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }


    /**
     * Generate the OPTIONS request for the resource type summaries
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $get = Get::init()->
            setParameters('api.resource-type.summary-parameters')->
            setDescription('route-descriptions.summary-resource-type-GET-index')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            setSearchable('api.resource-type.summary-searchable')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
