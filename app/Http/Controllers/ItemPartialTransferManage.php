<?php

namespace App\Http\Controllers;

use App\Jobs\ClearCache;
use App\Models\ItemPartialTransfer;
use App\Models\Transformers\ItemPartialTransfer as ItemPartialTransferTransformer;
use App\Response\Cache;
use App\Request\Route;
use App\Request\Validate\ItemPartialTransfer as ItemPartialTransferValidator;
use App\Response\Responses;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * Partial transfer of items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransferManage extends Controller
{
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::ITEM_PARTIAL_TRANSFER_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser(in_array((int) $resource_type_id, $this->permitted_resource_types, true))
            ->setUserId($this->user_id);

        try {
            $partial_transfer = (new ItemPartialTransfer())->find($item_partial_transfer_id);


            if ($partial_transfer !== null) {
                $partial_transfer->delete();

                ClearCache::dispatchNow($cache_job_payload->payload());

                return Responses::successNoContent();
            }

            return Responses::failedToSelectModelForUpdateOrDelete();
        } catch (QueryException $e) {
            return Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return Responses::notFound(trans('entities.item-partial-transfer'));
        }
    }

    public function transfer(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route\Validate::item(
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
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        $new_resource_id = $this->hash->decode('resource', request()->input('resource_id'));

        if ($new_resource_id === false) {
            return Responses::unableToDecode();
        }

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::ITEM_PARTIAL_TRANSFER_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser(in_array((int) $resource_type_id, $this->permitted_resource_types, true))
            ->setUserId($this->user_id);

        try {
            $partial_transfer = new ItemPartialTransfer([
                'resource_type_id' => $resource_type_id,
                'from' => (int) $resource_id,
                'to' => $new_resource_id,
                'item_id' => $item_id,
                'percentage' => request()->input('percentage'),
                'transferred_by' => $this->user_id
            ]);
            $partial_transfer->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (QueryException $e) {
            return Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return Responses::failedToSaveModelForCreate();
        }

        $item_partial_transfer = (new ItemPartialTransfer())->single(
            (int) $resource_type_id,
            (int) $partial_transfer->id
        );

        if ($item_partial_transfer === null) {
            return Responses::notFound(trans('entities.item_partial_transfer'));
        }

        return response()->json(
            (new ItemPartialTransferTransformer($item_partial_transfer))->asArray(),
            201
        );
    }
}
