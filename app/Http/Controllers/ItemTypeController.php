<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\Pagination as UtilityPagination;
use App\Validators\Request\Route;
use App\Models\Transformers\ItemType as ItemTypeTransformer;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\SearchParameters;
use App\Validators\Request\SortParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage item types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTypeController extends Controller
{
    protected $allow_entire_collection = true;

    /**
     * Return all the item types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $search_parameters = SearchParameters::fetch(
            Config::get('api.item-type.searchable')
        );

        $total = (new ItemType())->totalCount($search_parameters);

        $sort_parameters = SortParameters::fetch(
            Config::get('api.item-type.sortable')
        );

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

        $headers = new Header();
        $headers->collection($pagination, count($item_types), $total);

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers->addSort($sort_header);
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers->addSearch($search_header);
        }

        return response()->json(
            array_map(
                function($item_type) {
                    return (new ItemTypeTransformer($item_type))->toArray();
                },
                $item_types
            ),
            200,
            $headers->headers()
        );
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

        $headers = new Header();
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
