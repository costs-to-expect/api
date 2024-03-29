<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HttpResponse\Header;
use App\HttpResponse\Response;
use App\Models\ItemType;
use App\HttpOptionResponse\ItemTypeCollection;
use App\HttpOptionResponse\ItemTypeItem;
use App\HttpRequest\Parameter;
use App\Models\Permission;
use App\Transformer\ItemType as ItemTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage the item types supported by the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTypeController extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the item types
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $cache_control = new \App\Cache\Control();
        $cache_control->setTtlOneYear();

        $cache_collection = new \App\Cache\Response\Collection();
        $cache_collection->setFromCache($cache_control->getByKey($request->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {
            $search_parameters = Parameter\Search::fetch(
                Config::get('api.item-type.searchable')
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.item-type.sortable')
            );

            $total = (new ItemType())->totalCount($search_parameters);

            $pagination = new \App\HttpResponse\Pagination($request->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $item_types = (new ItemType())->paginatedCollection(
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($item_type) {
                    return (new ItemTypeTransformer($item_type))->asArray();
                },
                $item_types
            );

            $headers = new Header();
            $headers->collection($pagination_parameters, count($item_types), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey($request->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single item type
     *
     * @param string $item_type_id
     *
     * @return JsonResponse
     */
    public function show(string $item_type_id): JsonResponse
    {
        if ((new Permission())->itemTypeExists((int) $item_type_id) === false) {
            Response::notFound(trans('entities.item-type'));
        }

        $item_type = (new ItemType())->single($item_type_id);

        if ($item_type === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-type'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemTypeTransformer($item_type))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the item type list
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $response = new ItemTypeCollection(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }

    /**
     * Generate the OPTIONS request for a specific item type
     *
     * @param string $item_type_id
     *
     * @return JsonResponse
     */
    public function optionsShow(string $item_type_id): JsonResponse
    {
        if ((new Permission())->itemTypeExists((int) $item_type_id) === false) {
            Response::notFound(trans('entities.item-type'));
        }

        $response = new ItemTypeItem(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
