<?php

namespace App\Http\Controllers;

use App\HttpResponse\Responses;
use App\ItemType\Entity;
use App\Jobs\ClearCache;
use App\Models\ItemPartialTransfer;
use App\Request\Validate\ItemPartialTransfer as ItemPartialTransferValidator;
use App\Transformers\ItemPartialTransfer as ItemPartialTransferTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransferManage extends Controller
{
    public function delete(
        $resource_type_id,
        $item_partial_transfer_id
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item-partial-transfer'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->deleteAllocatedExpense((int) $resource_type_id, (int) $item_partial_transfer_id),
            'game', 'simple-expense', 'simple-item' => Responses::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function deleteAllocatedExpense(
        int $resource_type_id,
        int $item_partial_transfer_id
    ): JsonResponse
    {
        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_PARTIAL_TRANSFER_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $partial_transfer = (new ItemPartialTransfer())->find($item_partial_transfer_id);

            if ($partial_transfer !== null) {
                $partial_transfer->delete();

                ClearCache::dispatch($cache_job_payload->payload());

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
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->transferAllocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game', 'simple-expense', 'simple-item' => Responses::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function transferAllocatedExpense(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse
    {
        $validator = (new ItemPartialTransferValidator)->create(
            [
                'resource_type_id' => $resource_type_id,
                'existing_resource_id' => $resource_id
            ]
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $new_resource_id = $this->hash->decode('resource', request()->input('resource_id'));

        if ($new_resource_id === false) {
            return Responses::unableToDecode();
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_PARTIAL_TRANSFER_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType(($resource_type_id)))
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
            $resource_type_id,
            $partial_transfer->id
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
