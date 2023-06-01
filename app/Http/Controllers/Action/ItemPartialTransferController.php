<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\HttpResponse\Response;
use App\ItemType\Select;
use App\Jobs\ClearCache;
use App\Models\ItemPartialTransfer;
use App\HttpRequest\Validate\ItemPartialTransfer as ItemPartialTransferValidator;
use App\Transformer\ItemPartialTransfer as ItemPartialTransferTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransferController extends Controller
{
    public function delete(
        $resource_type_id,
        $item_partial_transfer_id
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-partial-transfer'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->deleteAllocatedExpense((int) $resource_type_id, (int) $item_partial_transfer_id),
            'game' => Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function deleteAllocatedExpense(
        int $resource_type_id,
        int $item_partial_transfer_id
    ): JsonResponse {
        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_PARTIAL_TRANSFER_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setUserId($this->user_id);

        try {
            $partial_transfer = (new ItemPartialTransfer())->find($item_partial_transfer_id);

            if ($partial_transfer !== null) {
                $partial_transfer->delete();

                ClearCache::dispatchSync($cache_job_payload->payload());

                return Response::successNoContent();
            }

            return Response::failedToSelectModelForUpdateOrDelete();
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::notFound(trans('entities.item-partial-transfer'), $e);
        }
    }

    public function transfer(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->transferAllocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function transferAllocatedExpense(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse {
        $validator = (new ItemPartialTransferValidator())->create(
            [
                'resource_type_id' => $resource_type_id,
                'existing_resource_id' => $resource_id
            ]
        );

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $new_resource_id = $this->hash->decode('resource', request()->input('resource_id'));

        if ($new_resource_id === false) {
            return Response::unableToDecode();
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_PARTIAL_TRANSFER_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
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

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        $item_partial_transfer = (new ItemPartialTransfer())->single(
            $resource_type_id,
            $partial_transfer->id
        );

        if ($item_partial_transfer === null) {
            return Response::notFound(trans('entities.item_partial_transfer'));
        }

        return response()->json(
            (new ItemPartialTransferTransformer($item_partial_transfer))->asArray(),
            201
        );
    }
}
