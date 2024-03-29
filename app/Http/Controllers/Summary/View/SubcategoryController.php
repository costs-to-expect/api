<?php

namespace App\Http\Controllers\Summary\View;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\HttpResponse\Header;
use App\Models\Summary\Subcategory;
use App\HttpOptionResponse\SummarySubcategoryCollection;
use App\HttpRequest\Parameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the subcategories routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubcategoryController extends Controller
{
    /**
     * Return a summary of the subcategories
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, $resource_type_id, $category_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.category'));
        }

        $cache_control = new \App\Cache\Control($this->user_id);
        $cache_control->setTtlOneMonth();

        $cache_summary = new \App\Cache\Response\Summary();
        $cache_summary->setFromCache($cache_control->getByKey($request->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {
            $search_parameters = Parameter\Search::fetch(
                Config::get('api.subcategory.summary-searchable')
            );

            $summary = (new Subcategory())->totalCount(
                $resource_type_id,
                $category_id,
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
                'subcategories' => $total
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
     * Generate the OPTIONS request for the subcategories summary
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id, $category_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.category'));
        }

        $response = new SummarySubcategoryCollection($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
