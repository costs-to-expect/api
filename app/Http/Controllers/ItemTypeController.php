<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use App\Option\Get;
use App\Response\Cache;
use App\Response\Header\Headers;
use App\Utilities\Pagination as UtilityPagination;
use App\Validators\Route;
use App\Models\Transformers\ItemType as ItemTypeTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\SearchParameters;
use App\Validators\SortParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage the item types supported by the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
    public function index(): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneYear();

        $search_parameters = SearchParameters::fetch(
            array_keys(Config::get('api.item-type.searchable'))
        );

        $sort_parameters = SortParameters::fetch(
            Config::get('api.item-type.sortable')
        );

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_collection->valid() === false) {
            $total = (new ItemType())->totalCount($search_parameters);

            $pagination = UtilityPagination::init(
                    request()->path(),
                    $total,
                    10,
                    $this->allow_entire_collection
                )->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                paging();

            $item_types = (new ItemType())->paginatedCollection(
                $pagination['offset'],
                $pagination['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function($item_type) {
                    return (new ItemTypeTransformer($item_type))->toArray();
                },
                $item_types
            );

            $headers = new Headers();
            $headers->collection($pagination, count($item_types), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addSearch(SearchParameters::xHeader())->
                addSort(SortParameters::xHeader());

            $cache_collection->create($total, $collection, $pagination, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
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
        Route::itemType((int) $item_type_id);

        $item_type = (new ItemType())->single($item_type_id);

        if ($item_type === null) {
            UtilityResponse::notFound(trans('entities.item-type'));
        }

        $headers = new Headers();
        $headers->item();

        return response()->json(
            (new ItemTypeTransformer($item_type))->toArray(),
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
        $get = Get::init()->
            setSortable('api.item-type.sortable')->
            setSearchable('api.item-type.searchable')->
            setPaginationOverride(true)->
            setDescription('route-descriptions.item_type_GET_index')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        return $this->optionsResponse(
            $get,
            200
        );
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
        Route::itemType($item_type_id);

        $get = Get::init()->
            setDescription('route-descriptions.item_type_GET_show')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        return $this->optionsResponse(
            $get,
            200
        );
    }
}
