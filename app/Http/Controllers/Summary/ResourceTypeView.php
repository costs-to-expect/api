<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Models\Summary\ResourceType;
use App\Option\SummaryResourceTypeCollection;
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
class ResourceTypeView extends Controller
{
    /**
     * Return a summary of the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cache_control = new Cache\Control(true, $this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                Config::get('api.resource-type.summary-searchable')
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
            $cache_control->putByKey(request()->getRequestUri(), $cache_summary->content());
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
        $response = new SummaryResourceTypeCollection(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
