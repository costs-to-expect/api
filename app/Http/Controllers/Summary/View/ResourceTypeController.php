<?php

namespace App\Http\Controllers\Summary\View;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\HttpResponse\Header;
use App\Models\Summary\ResourceType;
use App\HttpOptionResponse\SummaryResourceTypeCollection;
use App\HttpRequest\Parameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the resource-type routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    /**
     * Return a summary of the resource types
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $cache_control = new \App\Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_summary = new \App\Cache\Response\Summary();
        $cache_summary->setFromCache($cache_control->getByKey($request->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {
            $search_parameters = Parameter\Search::fetch(
                Config::get('api.resource-type.summary-searchable')
            );

            $summary = (new ResourceType())->totalCount(
                $this->viewable_resource_types,
                $search_parameters
            );

            $total = 0;
            $last_updated = null;
            if (count($summary) === 1 && array_key_exists('total', $summary[0])) {
                $total = (int) $summary[0]['total'];

                if (array_key_exists('last_updated', $summary[0])) {
                    $last_updated = $summary[0]['last_updated'];
                }
            }

            $collection = [
                'resource_types' => $total
            ];

            $headers = new Header();
            $headers
                ->addCacheControl($cache_control->visibility(), $cache_control->ttl())
                ->addETag($collection)
                ->addSearch(Parameter\Search::xHeader());

            if ($last_updated !== null) {
                $headers->addLastUpdated($last_updated);
            }

            $cache_summary->create($collection, $headers->headers());
            $cache_control->putByKey($request->getRequestUri(), $cache_summary->content());
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
