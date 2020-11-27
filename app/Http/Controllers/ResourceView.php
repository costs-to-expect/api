<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use App\AllowedValue\ItemSubtype;
use App\Option\ResourceCollection;
use App\Option\ResourceItem;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use App\Models\Resource;
use App\Transformers\Resource as ResourceTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceView extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the resources
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $cache_control = new Cache\Control(
            $this->writeAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneWeek();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                Config::get('api.resource.searchable')
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.resource.sortable')
            );

            $total = (new Resource())->totalCount(
                $resource_type_id,
                $this->viewable_resource_types,
                $search_parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $resources = (new Resource)->paginatedCollection(
                $resource_type_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $last_updated = null;
            if (count($resources) && array_key_exists('last_updated', $resources[0])) {
                $last_updated = $resources[0]['last_updated'];
            }

            $collection = array_map(
                static function ($resource) {
                    return (new ResourceTransformer($resource))->asArray();
                },
                $resources
            );

            $headers = new Headers();
            $headers
                ->collection($pagination_parameters, count($resources), $total)
                ->addCacheControl($cache_control->visibility(), $cache_control->ttl())
                ->addETag($collection)
                ->addSearch(Parameter\Search::xHeader())
                ->addSort(Parameter\Sort::xHeader());

            if ($last_updated !== null) {
                $headers->addLastUpdated($last_updated);
            }

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single resource
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function show(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $resource = (new Resource)->single($resource_type_id, $resource_id);

        if ($resource === null) {
            return \App\Response\Responses::notFound(trans('entities.resource'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ResourceTransformer($resource))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $response = new ResourceCollection($this->permissions((int) $resource_type_id));

        return $response
            ->setAllowedValues((new ItemSubtype())->allowedValues($resource_type['resource_type_item_type_id']))
            ->create()
            ->response();
    }

    public function optionsShow(string $resource_type_id, string $resource_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $resource = (new Resource)->single(
            $resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            return \App\Response\Responses::notFound(trans('entities.resource'));
        }

        $response = new ResourceItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
