<?php

namespace App\Http\Controllers;

use App\Entity\Item\Entity;
use App\Models\ItemPartialTransfer;
use App\Models\Transformers\ItemPartialTransfer as ItemPartialTransferTransformer;
use App\Option\ItemPartialTransferCollection;
use App\Option\ItemPartialTransferItem;
use App\Option\ItemPartialTransferTransfer;
use App\Response\Cache;
use App\Response\Header\Headers;
use App\Request\Parameter;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Partial transfer of items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransferView extends Controller
{
    /**
     * Return the partial transfer collection
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $cache_control = new Cache\Control(
            $this->writeAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneWeek();

        $entity = Entity::item($resource_type_id);
        if ($entity->allowPartialTransfers() === false) {
            return \App\Response\Responses::notSupported();
        }

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

            $parameters = Parameter\Request::fetch(
                array_keys(Config::get('api.item-partial-transfer.parameters.collection'))
            );

            $total = (new ItemPartialTransfer())->total(
                (int) $resource_type_id,
                $this->viewable_resource_types,
                $parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setParameters($parameters)->
                parameters();

            $transfers = (new ItemPartialTransfer())->paginatedCollection(
                (int)$resource_type_id,
                $this->viewable_resource_types,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $parameters
            );

            $collection = array_map(
                static function ($transfer) {
                    return (new ItemPartialTransferTransformer($transfer))->asArray();
                },
                $transfers
            );

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($transfers), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection);

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single item partial transfer
     *
     * @param $resource_type_id
     * @param $item_partial_transfer_id
     *
     * @return JsonResponse
     */
    public function show(
        $resource_type_id,
        $item_partial_transfer_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $entity = Entity::item($resource_type_id);
        if ($entity->allowPartialTransfers() === false) {
            return \App\Response\Responses::notSupported();
        }

        $item_partial_transfer = (new ItemPartialTransfer())->single(
            (int) $resource_type_id,
            (int) $item_partial_transfer_id
        );

        if ($item_partial_transfer === null) {
            return \App\Response\Responses::notFound(trans('entities.item_partial_transfer'));
        }

        $headers = new Headers();
        $headers->item();

        return response()->json(
            (new ItemPartialTransferTransformer($item_partial_transfer))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex($resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $entity = Entity::item($resource_type_id);
        if ($entity->allowPartialTransfers() === false) {
            return \App\Response\Responses::notSupported();
        }

        $response = new ItemPartialTransferCollection($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }

    /**
     * Generate the OPTIONS request for a specific item partial transfer
     *
     * @param $resource_type_id
     * @param $item_partial_transfer_id
     *
     * @return JsonResponse
     */
    public function optionsShow($resource_type_id, $item_partial_transfer_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-partial-transfer'));
        }

        $entity = Entity::item($resource_type_id);
        if ($entity->allowPartialTransfers() === false) {
            return \App\Response\Responses::notSupported();
        }

        $response = new ItemPartialTransferItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }

    public function optionsTransfer(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $entity = Entity::item($resource_type_id);
        if ($entity->allowPartialTransfers() === false) {
            return \App\Response\Responses::notSupported();
        }

        $response = new ItemPartialTransferTransfer($this->permissions((int) $resource_type_id));

        return $response->setAllowedValues(
                (new \App\AllowedValue\Resource())->allowedValues(
                    $resource_type_id,
                    $resource_id
                )
            )->
            create()->
            response();
    }
}
