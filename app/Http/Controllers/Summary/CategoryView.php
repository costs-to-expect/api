<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\HttpResponse\Header;
use App\Models\Summary\Category;
use App\HttpOptionResponse\SummaryCategoryCollection;
use App\HttpRequest\Parameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the categories routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryView extends Controller
{
    /**
     * Return a summary of the categories
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     * @throws \JsonException
     */
    public function index($resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $cache_control = new \App\Cache\Control(
            $this->hasWriteAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneMonth();

        $cache_summary = new \App\Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {
            $search_parameters = Parameter\Search::fetch(
                Config::get('api.category.summary-searchable')
            );

            $summary = (new Category())->total(
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
                'categories' => $total
            ];

            $headers = (new Header())
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
     * Generate the OPTIONS request for the categories summary
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $response = new SummaryCategoryCollection($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
