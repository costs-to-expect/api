<?php

namespace App\Http\Controllers;

use App\HttpResponse\Responses;
use App\ItemType\Select;
use App\Jobs\ClearCache;
use App\Models\Item;
use App\Models\ItemTransfer;
use App\HttpRequest\Validate\ItemTransfer as ItemTransferValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTransferManage extends Controller
{
    public function transfer(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'simple-expense' => $this->transferItem((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game', 'simple-item' => Responses::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function transferItem(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse
    {
        $user_id = $this->user_id;

        $validator = (new ItemTransferValidator)->create(
            [
                'resource_type_id' => $resource_type_id,
                'existing_resource_id' => $resource_id
            ]
        );

        if ($validator->fails()) {
            return \App\HttpResponse\Responses::validationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_TRANSFER_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->isPermittedUser($this->hasWriteAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $new_resource_id = $this->hash->decode('resource', request()->input('resource_id'));

            if ($new_resource_id === false) {
                return \App\HttpResponse\Responses::unableToDecode();
            }

            DB::transaction(static function() use ($resource_type_id, $resource_id, $item_id, $new_resource_id, $user_id) {
                $item = (new Item())->instance($resource_type_id, $resource_id, $item_id);
                if ($item !== null) {
                    $item->resource_id = $new_resource_id;
                    $item->save();
                } else {
                    return \App\HttpResponse\Responses::failedToSelectModelForUpdateOrDelete();
                }

                $item_transfer = new ItemTransfer([
                    'resource_type_id' => $resource_type_id,
                    'from' => $resource_id,
                    'to' => $new_resource_id,
                    'item_id' => $item_id,
                    'transferred_by' => $user_id
                ]);
                return $item_transfer->save();
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (QueryException $e) {
            return \App\HttpResponse\Responses::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return \App\HttpResponse\Responses::failedToSaveModelForUpdate($e);
        }

        return \App\HttpResponse\Responses::successNoContent();
    }
}
