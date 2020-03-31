<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemTransfer;
use App\Models\Resource;
use App\Option\Post;
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
            $item->resource_id = $new_resource_id;
            $item->save();

            $item_transfer = new ItemTransfer([
                'resource_type_id' => $resource_type_id,
                'from' => intval($resource_id),
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

        // Endpoint should 404 after request so figure 204 better than redirect or 404
        UtilityResponse::successNoContent();
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
