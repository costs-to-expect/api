<?php

namespace App\Http\Controllers;

use App\Models\ItemSubtype;
use App\Option\ItemSubtypeCollection;
use App\Option\ItemSubtypeItem;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use App\Models\Transformers\ItemSubtype as ItemSubtypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubtypeView extends Controller
{
    protected bool $allow_entire_collection = true;

    public function index($item_type_id): JsonResponse
    {
        Route\Validate::itemType($item_type_id);

        $cache_control = new Cache\Control();
        $cache_control->setTtlOneYear();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

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

            $pagination = new UtilityPagination(request()->path(), $total);
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

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($subtypes), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    public function show($item_type_id, $item_subtype_id): JsonResponse
    {
        Route\Validate::itemSubType($item_type_id, $item_subtype_id);

        $subtype = (new ItemSubtype())->single(
            (int) $item_type_id,
            (int) $item_subtype_id
        );

        if ($subtype === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subtype'));
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
        Route\Validate::itemType($item_type_id);

        $response = new ItemSubtypeCollection(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }

    public function optionsShow($item_type_id, $item_subtype_id): JsonResponse
    {
        Route\Validate::itemSubType(
            $item_type_id,
            $item_subtype_id
        );

        $response = new ItemSubtypeItem(['view'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
