<?php

namespace App\Http\Controllers;

use App\Models\ItemTransfer;
use App\Models\Transformers\ItemTransfer as ItemTransferTransformer;
use App\Option\ItemTransferCollection;
use App\Option\ItemTransferItem;
use App\Option\ItemTransferTransfer;
use App\Response\Cache;
use App\Response\Header\Headers;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Transfer items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTransferView extends Controller
{
    /**
     * Return the item transfers collection
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $parameters = Parameter\Request::fetch(
                array_keys(Config::get('api.item-transfer.parameters.collection'))
            );

            $total = (new ItemTransfer())->total(
                (int)$resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                $parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setParameters($parameters)->
                parameters();

            $transfers = (new ItemTransfer())->paginatedCollection(
                (int)$resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $parameters
            );

            $collection = array_map(
                static function ($transfer) {
                    return (new ItemTransferTransformer($transfer))->asArray();
                },
                $transfers
            );

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($transfers), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection);

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Generate the OPTIONS request for the transfers collection
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $response = new ItemTransferCollection($permissions);

        return $response->create()->response();
    }

    /**
     * Generate the OPTIONS request for a specific item transfer
     *
     * @param $resource_type_id
     * @param $item_transfer_id
     *
     * @return JsonResponse
     */
    public function optionsShow($resource_type_id, $item_transfer_id): JsonResponse
    {
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $response = new ItemTransferItem($permissions);

        return $response->create()->response();
    }

    public function optionsTransfer(
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
            $this->permitted_resource_types
        );

        $response = new ItemTransferTransfer($permissions);

        return $response->setAllowedValues(
                (new \App\Option\Value\Resource())->allowedValues(
                    $resource_type_id,
                    $resource_id
                )
            )->
            create()->
            response();
    }

    /**
     * Return a single item transfer
     *
     * @param $resource_type_id
     * @param $item_transfer_id
     *
     * @return JsonResponse
     */
    public function show(
        $resource_type_id,
        $item_transfer_id
    ): JsonResponse
    {
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $item_transfer = (new ItemTransfer())->single(
            (int) $resource_type_id,
            (int) $item_transfer_id
        );

        if ($item_transfer === null) {
            \App\Response\Responses::notFound(trans('entities.item_transfer'));
        }

        $headers = new Headers();
        $headers->item();

        return response()->json(
            (new ItemTransferTransformer($item_transfer))->asArray(),
            200,
            $headers->headers()
        );
    }
}
