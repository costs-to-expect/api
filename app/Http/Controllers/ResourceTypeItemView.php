<?php

namespace App\Http\Controllers;

use App\Entity\Item\Entity;
use App\Option\ResourceTypeItemCollection;
use App\Response\Cache;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;

/**
 * View items for all resources for a resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemView extends Controller
{
    /**
     * Return all the items based on the set filter options
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

            $entity = Entity::item($resource_type_id);

            $resource_type_item_model = $entity->resourceTypeModel();

            $collection_parameters = Parameter\Request::fetch(
                array_keys($entity->resourceTypeRequestParameters()),
                $resource_type_id
            );

            $sort_fields = Parameter\Sort::fetch(
                $entity->resourceTypeSortParameters()
            );

            $search_parameters = Parameter\Search::fetch(
                $entity->resourceTypeSearchParameters()
            );

            $filter_parameters = Parameter\Filter::fetch(
                $entity->resourceTypeFilterParameters()
            );

            $total = $resource_type_item_model->totalCount(
                $resource_type_id,
                $collection_parameters,
                $search_parameters,
                $filter_parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_fields)->
                setParameters($collection_parameters)->
                setFilteringParameters($filter_parameters)->
                parameters();


            $items = $resource_type_item_model->paginatedCollection(
                $resource_type_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $collection_parameters,
                $search_parameters,
                $filter_parameters,
                $sort_fields
            );

            $collection = array_map(
                static function ($item) use ($entity) {
                    return $entity->resourceTypeTransformer($item)->asArray();
                },
                $items
            );

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($items), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader())->
                addParameters(Parameter\Request::xHeader())->
                addFilters(Parameter\Filter::xHeader());

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Generate the OPTIONS request for the items list
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $entity = Entity::item($resource_type_id);

        $permissions = Route\Permission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $response = new ResourceTypeItemCollection($permissions);

        return $response
            ->setEntity($entity)
            ->setAllowedValues(
                $entity->allowedValuesForResourceTypeItemCollection(
                    $resource_type_id,
                    $this->permitted_resource_types,
                    $this->include_public
                )
            )
            ->create()
            ->response();
    }
}
