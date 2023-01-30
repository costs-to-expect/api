<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HttpResponse\Header;
use App\HttpResponse\Response;
use App\Models\ItemSubtype;
use App\HttpOptionResponse\ItemSubtypeCollection;
use App\HttpOptionResponse\ItemSubtypeItem;
use App\HttpRequest\Parameter;
use App\Models\Permission;
use App\Transformer\ItemSubtype as ItemSubtypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubtypeController extends Controller
{
    protected bool $allow_entire_collection = true;

    public function index(Request $request, $item_type_id): JsonResponse
    {
        if ((new Permission())->itemTypeExists((int) $item_type_id) === false) {
            return Response::notFound(trans('entities.item-subtype'));
        }

        $cache_control = new \App\Cache\Control();
        $cache_control->setTtlOneYear();

        $cache_collection = new \App\Cache\Response\Collection();
        $cache_collection->setFromCache($cache_control->getByKey($request->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {
            $search_parameters = Parameter\Search::fetch(
                Config::get('api.item-subtype.searchable')
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.item-subtype.sortable')
            );

            $total = (new ItemSubtype())->totalCount(
                (int) $item_type_id,
                $search_parameters
            );

            $pagination = new \App\HttpResponse\Pagination($request->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $subtypes = (new ItemSubtype())->paginatedCollection(
                (int) $item_type_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($subtype) {
                    return (new ItemSubtypeTransformer($subtype))->asArray();
                },
                $subtypes
            );

            $headers = new Header();
            $headers->collection($pagination_parameters, count($subtypes), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey($request->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    public function show($item_type_id, $item_subtype_id): JsonResponse
    {
        if ((new Permission())->itemSubTypeExists((int) $item_type_id, (int) $item_subtype_id) === false) {
            return Response::notFound(trans('entities.item-subtype'));
        }

        $subtype = (new ItemSubtype())->single(
            (int) $item_type_id,
            (int) $item_subtype_id
        );

        if ($subtype === null) {
            return Response::notFound(trans('entities.item-subtype'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemSubtypeTransformer($subtype))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex($item_type_id): JsonResponse
    {
        if ((new Permission())->itemTypeExists((int) $item_type_id) === false) {
            return Response::notFound(trans('entities.item-subtype'));
        }

        $response = new ItemSubtypeCollection(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }

    public function optionsShow($item_type_id, $item_subtype_id): JsonResponse
    {
        if ((new Permission())->itemSubTypeExists((int) $item_type_id, (int) $item_subtype_id) === false) {
            return Response::notFound(trans('entities.item-subtype'));
        }

        $response = new ItemSubtypeItem(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
