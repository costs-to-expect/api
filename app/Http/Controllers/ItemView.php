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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
        );

        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $entity_config = Entity::item($resource_type_id);

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $parameters = Parameter\Request::fetch(
                array_keys($entity_config->requestParameters()),
                (int) $resource_type_id,
                (int) $resource_id
            );

            $search_parameters = Parameter\Search::fetch(
                $entity_config->searchParameters()
            );

            $filter_parameters = Parameter\Filter::fetch(
                $entity_config->filterParameters()
            );

            $sort_parameters = Parameter\Sort::fetch(
                $entity_config->sortParameters()
            );

            $item_model = $entity_config->model();

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
                static function ($item) use ($entity_config) {
                    return $entity_config->transformer($item)->asArray();
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
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
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
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $entity_config = Entity::item($resource_type_id);

        $parameters = Parameter\Request::fetch(
            array_keys($entity_config->itemRequestParameters()),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $item_model = $entity_config->model();

        $item = $item_model->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $parameters
        );

        if ($item === null) {
            \App\Response\Responses::notFound(trans('entities.item'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            $entity_config->transformer($item)->asArray(),
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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
        );

        $entity_config = Entity::item($resource_type_id);

        $permissions = Route\Permission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
        );

        $defined_parameters = Parameter\Request::fetch(
            array_keys($entity_config->requestParameters()),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $allowed_values = (new \App\Option\AllowedValues\Item($entity_config))->allowedValues(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            $this->include_public,
            array_keys($entity_config->requestParameters()),
            $defined_parameters
        );

        $response = new ItemCollection($permissions);

        return $response->setEntityConfig($entity_config)
            ->setAllowedValues($allowed_values)
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
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
        );

        $entity_config = Entity::item($resource_type_id);

        $item_model = $entity_config->model();

        $item = $item_model->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            \App\Response\Responses::notFound(trans('entities.item'));
        }

        $response = new ItemItem($permissions);

        return $response->setEntityConfig(Entity::item($resource_type_id))
            ->create()
            ->response();
    }
}
