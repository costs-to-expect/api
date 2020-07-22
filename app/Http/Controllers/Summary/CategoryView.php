<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Models\Summary\Category;
use App\Option\SummaryCategoryCollection;
use App\Response\Cache;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the categories routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
     */
    public function index($resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneMonth();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                array_keys(Config::get('api.category.summary-searchable'))
            );

            $summary = (new Category())->total(
                $resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                $search_parameters
            );

            $collection = [
                'categories' => $summary
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
     * Generate the OPTIONS request for the categories summary
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $response = new SummaryCategoryCollection($permissions);

        return $response->create()->response();
    }
}
