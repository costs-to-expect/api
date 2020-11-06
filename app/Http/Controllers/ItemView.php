<?php

namespace App\Http\Controllers;

use App\Entity\Item\Entity;
use App\Option\ItemCollection;
use App\Option\ItemItem;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemView extends Controller
{
    /**
     * Return all the items for the resource type and resource applying
     * any filtering, pagination and ordering
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function index(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $cache_control = new Cache\Control(
            $this->writeAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneWeek();

        $entity = Entity::item($resource_type_id);

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

            $parameters = Parameter\Request::fetch(
                array_keys($entity->requestParameters()),
                (int) $resource_type_id,
                (int) $resource_id
            );

            $search_parameters = Parameter\Search::fetch(
                $entity->searchParameters()
            );

            $filter_parameters = Parameter\Filter::fetch(
                $entity->filterParameters()
            );

            $sort_parameters = Parameter\Sort::fetch(
                $entity->sortParameters()
            );

            $item_model = $entity->model();

            $total = $item_model->totalCount(
                $resource_type_id,
                $resource_id,
                $parameters,
                $search_parameters,
                $filter_parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setParameters($parameters)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                setFilteringParameters($filter_parameters)->
                parameters();

            $items = $item_model->paginatedCollection(
                $resource_type_id,
                $resource_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $parameters,
                $search_parameters,
                $filter_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($item) use ($entity) {
                    return $entity->transformer($item)->asArray();
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
     * Return a single item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function show(
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $entity = Entity::item($resource_type_id);

        $parameters = Parameter\Request::fetch(
            array_keys($entity->itemRequestParameters()),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $item_model = $entity->model();

        $item = $item_model->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $parameters
        );

        if ($item === null) {
            return \App\Response\Responses::notFound(trans('entities.item'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            $entity->transformer($item)->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $entity = Entity::item($resource_type_id);

        $permissions = Route\Permission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
        );

        $response = new ItemCollection($permissions);

        return $response
            ->setEntity($entity)
            ->setAllowedValues(
                $entity->allowedValuesForItemCollection(
                    $resource_type_id,
                    $resource_id,
                    $this->permitted_resource_types,
                    $this->include_public
                )
            )
            ->create()
            ->response();
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $permissions = Route\Permission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
        );

        $entity = Entity::item($resource_type_id);

        $item_model = $entity->model();

        $item = $item_model->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return \App\Response\Responses::notFound(trans('entities.item'));
        }

        $response = new ItemItem($permissions);

        return $response
            ->setEntity($entity)
            ->setAllowedValues($entity->allowedValuesForItem((int) $resource_type_id))
            ->create()
            ->response();
    }
}
