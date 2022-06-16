<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\HttpResponse\Header;
use App\Models\Summary\Resource;
use App\HttpOptionResponse\SummaryResourceCollection;
use App\HttpRequest\Parameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the resource routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceView extends Controller
{
    /**
     * Return a summary of the resources
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $cache_control = new \App\Cache\Control(
            $this->hasWriteAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new \App\Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                Config::get('api.resource.summary-searchable')
            );

            $summary = (new Resource())->totalCount(
                $resource_type_id,
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
                'resources' => $total
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
            $cache_control->putByKey(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }


    /**
     * Generate the OPTIONS request for the resource summary
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $response = new SummaryResourceCollection($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
