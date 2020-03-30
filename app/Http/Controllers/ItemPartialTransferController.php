<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemPartialTransfer;
use App\Models\Resource;
use App\Option\Post;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\Response as UtilityResponse;
use App\Utilities\RoutePermission;
use App\Validators\Fields\ItemPartialTransfer as ItemPartialTransferValidator;
use App\Validators\Route;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Partial transfer of items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransferController extends Controller
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
                'from' => intval($resource_id),
                'to' => $new_resource_id,
                'item_id' => $item_id,
                'percentage' => request()->input('percentage'),
                'transferred_by' => Auth::user()->id
            ]);
            $partial_transfer->save();
        } catch (QueryException $e) {
            UtilityResponse::foreignKeyConstraintError();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForCreate();
        }

        return UtilityResponse::successNoContent();
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
