<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HttpResponse\Header;
use App\Models\Subcategory;
use App\HttpOptionResponse\SubcategoryCollection;
use App\HttpOptionResponse\SubcategoryItem;
use App\HttpRequest\Parameter;
use App\Transformer\Subcategory as SubcategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubcategoryView extends Controller
{
    protected bool $allow_entire_collection = true;

    public function index(Request $request, $resource_type_id, $category_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.category'));
        }

        $cache_control = new \App\Cache\Control(
            $this->hasWriteAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneMonth();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey($request->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {
            $search_parameters = Parameter\Search::fetch(
                Config::get('api.subcategory.searchable')
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.subcategory.sortable')
            );

            $total = (new Subcategory())->totalCount(
                (int)$resource_type_id,
                (int)$category_id,
                $search_parameters
            );

            $pagination = new \App\HttpResponse\Pagination($request->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $subcategories = (new Subcategory())->paginatedCollection(
                (int)$resource_type_id,
                (int)$category_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $last_updated = null;
            if (count($subcategories) && array_key_exists('last_updated', $subcategories[0])) {
                $last_updated = $subcategories[0]['last_updated'];
            }

            $collection = array_map(
                static function ($subcategory) {
                    return (new SubcategoryTransformer($subcategory))->asArray();
                },
                $subcategories
            );

            $headers = new Header();
            $headers
                ->collection($pagination_parameters, count($subcategories), $total)
                ->addCacheControl($cache_control->visibility(), $cache_control->ttl())
                ->addETag($collection)
                ->addSearch(Parameter\Search::xHeader())
                ->addSort(Parameter\Sort::xHeader());

            if ($last_updated !== null) {
                $headers->addLastUpdated($last_updated);
            }

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey($request->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single sub category
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function show(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.subcategory'));
        }

        $subcategory = (new Subcategory())->single(
            $category_id,
            $subcategory_id
        );

        if ($subcategory === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.subcategory'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new SubcategoryTransformer($subcategory))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories list
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

        $response = new SubcategoryCollection($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }

    /**
     * Generate the OPTIONS request for the specific sub category
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.subcategory'));
        }

        $response = new SubcategoryItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
