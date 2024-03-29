<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HttpOptionResponse\ResourceCollection;
use App\HttpOptionResponse\ResourceItem;
use App\HttpRequest\Parameter;
use App\HttpResponse\Header;
use App\Models\AllowedValue\ItemSubtype;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Transformer\Resource as ResourceTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceController extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the resources
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $cache_control = new \App\Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Response\Collection();
        $cache_collection->setFromCache($cache_control->getByKey($request->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {
            $request_parameters = Parameter\Request::fetch(
                array_keys(Config::get('api.resource.parameters'))
            );

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

            $pagination = new \App\HttpResponse\Pagination($request->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)
                ->setSearchParameters($search_parameters)
                ->setSortParameters($sort_parameters)
                ->setParameters($request_parameters)
                ->parameters();

            $resources = (new Resource())->paginatedCollection(
                $resource_type_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters,
                $request_parameters
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

            $headers = new Header();
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
            $cache_control->putByKey($request->getRequestUri(), $cache_collection->content());
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
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $resource = (new Resource())->single($resource_type_id, $resource_id);

        if ($resource === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.resource'));
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
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $response = new ResourceCollection($this->permissions((int) $resource_type_id));

        return $response->setAllowedValuesForFields(
            (new ItemSubtype())->allowedValues(
                    $resource_type['resource_type_item_type_id']
                )
        )
            ->setAllowedValuesForParameters(
                (new ItemSubtype())->allowedValues(
                    $resource_type['resource_type_item_type_id'],
                    'item-subtype'
                )
            )
            ->create()
            ->response();
    }

    public function optionsShow(string $resource_type_id, string $resource_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $resource = (new Resource())->single(
            $resource_type_id,
            $resource_id
        );

        if ($resource === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.resource'));
        }

        $response = new ResourceItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
