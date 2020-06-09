<?php

namespace App\Http\Controllers;

use App\Models\ItemPartialTransfer;
use App\Models\Resource;
use App\Models\Transformers\ItemPartialTransfer as ItemPartialTransferTransformer;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Post;
use App\Response\Cache;
use App\Response\CacheControl;
use App\Utilities\Header;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\Response as UtilityResponse;
use App\Utilities\RoutePermission;
use App\Validators\Fields\ItemPartialTransfer as ItemPartialTransferValidator;
use App\Validators\Parameters;
use App\Validators\Route;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Monolog\Utils;

/**
 * Partial transfer of items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransferController extends Controller
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
        $cache_control = new CacheControl($this->user_id);
        $cache_control->setTtlOneWeek();

        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $parameters = Parameters::fetch(
            array_keys(Config::get('api.item-transfer.parameters.collection'))
        );

        $cache = new Cache();
        $cache->setContent($cache_control->get(request()->getRequestUri()));

        if ($cache->valid() === false) {

            $total = (new ItemPartialTransfer())->total(
                (int)$resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                $parameters
            );

            $pagination = UtilityPagination::init(
                request()->path(),
                $total,
                10,
                $this->allow_entire_collection
            )->paging();

            $transfers = (new ItemPartialTransfer())->paginatedCollection(
                (int)$resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                $pagination['offset'],
                $pagination['limit'],
                $parameters
            );

            $collection = array_map(
                static function ($transfer) {
                    return (new ItemPartialTransferTransformer($transfer))->toArray();
                },
                $transfers
            );

            $headers = new Header();
            $headers->collection($pagination, count($transfers), $total);
            $headers->addCacheControl(
                $cache_control->visibility(),
                $cache_control->ttl()
            );

            $cache->setCollection($collection);
            $cache->setTotal($total);
            $cache->setPagination($pagination);
            $cache->setHeaders($headers->headers());

            $cache_control->put(request()->getRequestUri(), $cache->content());
        }

        return response()->json($cache->collection(), 200, $cache->headers());
    }

    /**
     * Delete the requested partial transfer
     *
     * @param $resource_type_id
     * @param $item_partial_transfer_id
     *
     * @return JsonResponse
     */
    public function delete(
        $resource_type_id,
        $item_partial_transfer_id
    ): JsonResponse
    {
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        try {
            $partial_transfer = (new ItemPartialTransfer())->find($item_partial_transfer_id);

            if ($partial_transfer !== null) {
                $partial_transfer->delete();
                return UtilityResponse::successNoContent();
            }

            return UtilityResponse::failedToSelectModelForUpdateOrDelete();
        } catch (QueryException $e) {
            return UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            return UtilityResponse::notFound(trans('entities.item-partial-transfer'), $e);
        }
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
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $item_partial_transfer = (new ItemPartialTransfer())->single(
            (int) $resource_type_id,
            (int) $item_partial_transfer_id
        );

        if ($item_partial_transfer === null) {
            UtilityResponse::notFound(trans('entities.item_partial_transfer'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemPartialTransferTransformer($item_partial_transfer))->toArray(),
            200,
            $headers->headers()
        );
    }

    public function transfer(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        $validator = (new ItemPartialTransferValidator)->create(
            [
                'resource_type_id' => $resource_type_id,
                'existing_resource_id' => $resource_id
            ]
        );
        UtilityRequest::validateAndReturnErrors($validator);

        $new_resource_id = $this->hash->decode('resource', request()->input('resource_id'));

        if ($new_resource_id === false) {
            UtilityResponse::unableToDecode();
        }

        try {
            $partial_transfer = new ItemPartialTransfer([
                'resource_type_id' => $resource_type_id,
                'from' => (int) $resource_id,
                'to' => $new_resource_id,
                'item_id' => $item_id,
                'percentage' => request()->input('percentage'),
                'transferred_by' => Auth::user()->id
            ]);
            $partial_transfer->save();
        } catch (QueryException $e) {
            return UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            return UtilityResponse::failedToSaveModelForCreate();
        }

        $item_partial_transfer = (new ItemPartialTransfer())->single(
            (int) $resource_type_id,
            (int) $partial_transfer->id
        );

        if ($item_partial_transfer === null) {
            return UtilityResponse::notFound(trans('entities.item_partial_transfer'));
        }

        return response()->json(
            (new ItemPartialTransferTransformer($item_partial_transfer))->toArray(),
            201
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
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
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
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
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
        Route::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $post = Post::init()->
            setFields('api.item-partial-transfer.fields')->
            setFieldsData(
                $this->fieldsData(
                    $resource_type_id,
                    $resource_id
                )
            )->
            setDescription('route-descriptions.item_partial_transfer_POST')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse($post, 200);
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
                UtilityResponse::unableToDecode();
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
