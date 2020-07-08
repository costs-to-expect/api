<?php

namespace App\Http\Controllers;

use App\Models\ItemPartialTransfer;
use App\Models\Resource;
use App\Models\Transformers\ItemPartialTransfer as ItemPartialTransferTransformer;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Post;
use App\Response\Cache;
use App\Response\Header\Headers;
use App\Request\Parameter;
use App\Request\Route;
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
     * Return the categories collection
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $parameters = Parameter\Request::fetch(
                array_keys(Config::get('api.item-transfer.parameters.collection'))
            );

            $total = (new ItemPartialTransfer())->total(
                (int)$resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                $parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setParameters($parameters)->
                parameters();

            $transfers = (new ItemPartialTransfer())->paginatedCollection(
                (int)$resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
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
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
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
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $item_partial_transfer = (new ItemPartialTransfer())->single(
            (int) $resource_type_id,
            (int) $item_partial_transfer_id
        );

        if ($item_partial_transfer === null) {
            \App\Response\Responses::notFound(trans('entities.item_partial_transfer'));
        }

        $headers = new Headers();
        $headers->item();

        return response()->json(
            (new ItemPartialTransferTransformer($item_partial_transfer))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the partial transfers collection
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

        $get = Get::init()->
            setParameters('api.item-partial-transfer.parameters.collection')->
            setPagination(true)->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_partial_transfer_GET_index')->
            option();

        return $this->optionsResponse(
            $get,
            200
        );
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
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setDescription('route-descriptions.item_partial_transfer_GET_show')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $delete = Delete::init()->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            setDescription('route-descriptions.item_partial_transfer_DELETE')->
            option();

        return $this->optionsResponse(
            $get + $delete,
            200
        );
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

        $post = Post::init()->
            setFields('api.item-partial-transfer.fields')->
            setDynamicFields(
            (new \App\Option\Value\Resource())->allowedValues(
                $resource_type_id,
                $resource_id
            ))->
            setDescription('route-descriptions.item_partial_transfer_POST')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse($post, 200);
    }
}
