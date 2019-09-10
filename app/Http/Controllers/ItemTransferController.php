<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Resource;
use App\Option\Post;
use App\Utilities\Request as UtilityRequest;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\Fields\ItemTransfer as ItemTransferValidator;
use App\Validators\Request\Route;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Transfer items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTransferController extends Controller
{
    protected $collection_parameters = [];
    protected $get_parameters = [];
    protected $pagination = [];

    public function transfer(
        Request $request,
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
            false
        );

        $validator = (new ItemTransferValidator)->create(
            [
                'resource_type_id' => $resource_type_id,
                'existing_resource_id' => $resource_id
            ]
        );
        UtilityRequest::validateAndReturnErrors($validator);

        try {
            $new_resource_id = $this->hash->decode('resource', $request->input('resource_id'));

            if ($new_resource_id === false) {
                UtilityResponse::unableToDecode();
            }

            $item = (new Item())->instance($resource_type_id, $resource_id, $item_id);
            $item->resource_id = $new_resource_id;
            $item->save();
        } catch (Exception $e) {
            UtilityResponse::failedToSaveModelForUpdate();
        }

        // Endpoint should 404 after request so figure 204 better than redirect or 404
        UtilityResponse::successNoContent();
    }

    public function optionsTransfer(
        Request $request,
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

        $post = Post::init()->
            setDescription('route-descriptions.item_transfer_POST')->
            setFields('api.item-transfer.fields')->
            setConditionalFields(
                $this->conditionalPostParameters(
                    $resource_type_id,
                    $resource_id
                )
            )->
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
    private function conditionalPostParameters(
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
