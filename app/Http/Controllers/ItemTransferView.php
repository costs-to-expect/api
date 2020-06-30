<?php

namespace App\Http\Controllers;

use App\Models\ItemTransfer;
use App\Models\Resource;
use App\Models\Transformers\ItemTransfer as ItemTransferTransformer;
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

        $get = Get::init()->
            setParameters('api.item-transfer.parameters.collection')->
            setPagination(true)->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.item_transfer_GET_index')->
            option();

        return $this->optionsResponse(
            $get,
            200
        );
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

        $get = Get::init()->
            setDescription('route-descriptions.item_transfer_GET_show')->
            setAuthenticationStatus($permissions['view'])->
            option();

        return $this->optionsResponse(
            $get,
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
            setFields('api.item-transfer.fields')->
            setFieldsData(
                $this->fieldsData(
                    $resource_type_id,
                    $resource_id
                )
            )->
            setDescription('route-descriptions.item_transfer_POST')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse($post, 200);
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

    /**
     * Generate any conditional POST parameters, will be merged with the
     * relevant config/api/[type]/fields.php data array
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     *
     * @return array
     */
    private function fieldsData(
        int $resource_type_id,
        int $resource_id
    ): array
    {
        $resources = (new Resource())->resourcesForResourceType(
            $resource_type_id,
            $resource_id
        );

        $conditional_post_parameters = ['resource_id' => []];
        foreach ($resources as $resource) {
            $id = $this->hash->encode('resource', $resource['resource_id']);

            if ($id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $conditional_post_parameters['resource_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $resource['resource_name'],
                'description' => $resource['resource_description']
            ];
        }

        return $conditional_post_parameters;
    }
}
