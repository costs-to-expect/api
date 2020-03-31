<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemTransfer;
use App\Models\Resource;
use App\Models\Transformers\ItemTransfer as ItemTransferTransformer;
use App\Option\Get;
use App\Option\Post;
use App\Utilities\Header;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\Response as UtilityResponse;
use App\Utilities\RoutePermission;
use App\Validators\Fields\ItemTransfer as ItemTransferValidator;
use App\Validators\Route;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Transfer items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTransferController extends Controller
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
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $total = (new ItemTransfer())->total(
            (int) $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public
        );

        $pagination = UtilityPagination::init(
                request()->path(),
                $total,
                10,
                $this->allow_entire_collection
            )->
            paging();

        $transfers = (new ItemTransfer())->paginatedCollection(
            (int) $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public,
            $pagination['offset'],
            $pagination['limit']
        );

        $headers = new Header();
        $headers->collection($pagination, count($transfers), $total);

        return response()->json(
            array_map(
                static function($transfer) {
                    return (new ItemTransferTransformer($transfer))->toArray();
                },
                $transfers
            ),
            200,
            $headers->headers()
        );
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
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
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
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
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
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $item_transfer = (new ItemTransfer())->single(
            (int) $resource_type_id,
            (int) $item_transfer_id
        );

        if ($item_transfer === null) {
            UtilityResponse::notFound(trans('entities.item_transfer'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemTransferTransformer($item_transfer))->toArray(),
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

        $validator = (new ItemTransferValidator)->create(
            [
                'resource_type_id' => $resource_type_id,
                'existing_resource_id' => $resource_id
            ]
        );
        UtilityRequest::validateAndReturnErrors($validator);

        try {
            $new_resource_id = $this->hash->decode('resource', request()->input('resource_id'));

            if ($new_resource_id === false) {
                UtilityResponse::unableToDecode();
            }

            $item = (new Item())->instance($resource_type_id, $resource_id, $item_id);
            if ($item !== null) {
                $item->resource_id = $new_resource_id;
                $item->save();
            } else {
                UtilityResponse::failedToSelectModelForUpdate();
            }

            $item_transfer = new ItemTransfer([
                'resource_type_id' => $resource_type_id,
                'from' => (int) $resource_id,
                'to' => $new_resource_id,
                'item_id' => $item_id,
                'transferred_by' => Auth::user()->id
            ]);
            $item_transfer->save();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForUpdate();
        }

        return UtilityResponse::successNoContent();
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
