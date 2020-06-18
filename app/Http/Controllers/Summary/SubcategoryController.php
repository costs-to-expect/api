<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Models\Summary\Subcategory;
use App\Option\Get;
use App\Response\Cache;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the subcategories routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
    public function index($resource_type_id, $category_id): JsonResponse
    {
        Route\Validate::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneMonth();

        $search_parameters = Parameter\Search::fetch(
            array_keys(Config::get('api.subcategory.summary-searchable'))
        );

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = (new Subcategory())->totalCount(
                $resource_type_id,
                $category_id,
                $search_parameters
            );

            $collection = [
                'subcategories' => $summary
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
     * Generate the OPTIONS request for the subcategories summary
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id, $category_id): JsonResponse
    {
        Route\Validate::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setDescription('route-descriptions.summary_subcategory_GET_index')->
            setAuthenticationStatus($permissions['view'])->
            setSearchable('api.subcategory.summary-searchable')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
