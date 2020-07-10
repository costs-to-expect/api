<?php

namespace App\Http\Controllers;

use App\Item\Factory;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Option\Value\Item;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;

/**
 * Manage items
 *
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

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $item_interface = Factory::item($resource_type_id);

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $parameters = Parameter\Request::fetch(
                array_keys($item_interface->collectionParameters()),
                (int) $resource_type_id,
                (int) $resource_id
            );

            $search_parameters = Parameter\Search::fetch(
                $item_interface->searchParameters()
            );

            $filter_parameters = Parameter\Filter::fetch(
                $item_interface->filterParameters()
            );

            $sort_parameters = Parameter\Sort::fetch(
                $item_interface->sortParameters()
            );

            $item_model = $item_interface->model();
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
                static function ($item) use ($item_interface) {
                    return $item_interface->transformer($item)->asArray();
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

        $item_interface = Factory::item($resource_type_id);

        $parameters = Parameter\Request::fetch(
            array_keys($item_interface->showParameters()),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $item_model = $item_interface->model();

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
            $item_interface->transformer($item)->asArray(),
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

        $item_interface = Factory::item($resource_type_id);

        $permissions = Route\Permission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
        );

        $defined_parameters = Parameter\Request::fetch(
            array_keys($item_interface->collectionParameters()),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $allowed_values = (new Item())->allowedValues(
            $item_interface,
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            $this->include_public,
            $defined_parameters
        );

        $get = Get::init()->
            setSortable($item_interface->sortParametersConfig())->
            setSearchable($item_interface->searchParametersConfig())->
            setFilterable($item_interface->filterParametersConfig())->
            setParameters($item_interface->collectionParametersConfig())->
            setDynamicParameters($allowed_values)->
            setPagination(true)->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_GET_index')->
            option();

        $post = Post::init()->
            setFields($item_interface->fieldsConfig())->
            setDescription( 'route-descriptions.item_POST')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
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

        $item_interface = Factory::item($resource_type_id);

        $item_model = $item_interface->model();

        $item = $item_model->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            \App\Response\Responses::notFound(trans('entities.item'));
        }

        $get = Get::init()->
            setParameters($item_interface->showParametersConfig())->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_GET_show')->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.item_DELETE')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        $patch = Patch::init()->
            setFields($item_interface->fieldsConfig())->
            setDescription('route-descriptions.item_PATCH')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }
}