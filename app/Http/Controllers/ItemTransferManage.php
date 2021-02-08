<?php

namespace App\Http\Controllers;

use App\Jobs\ClearCache;
use App\Models\Item;
use App\Models\ItemTransfer;
use App\Response\Cache;
use App\Request\Validate\ItemTransfer as ItemTransferValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Transfer items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
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
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $user_id = $this->user_id;

        $validator = (new ItemTransferValidator)->create(
            [
                'resource_type_id' => $resource_type_id,
                'existing_resource_id' => $resource_id
            ]
        );

        if ($validator->fails()) {
            \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::ITEM_TRANSFER_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $new_resource_id = $this->hash->decode('resource', request()->input('resource_id'));

            if ($new_resource_id === false) {
                return \App\Response\Responses::unableToDecode();
            }

            DB::transaction(static function() use ($resource_type_id, $resource_id, $item_id, $new_resource_id, $user_id) {
                $item = (new Item())->instance($resource_type_id, $resource_id, $item_id);
                if ($item !== null) {
                    $item->resource_id = $new_resource_id;
                    $item->save();
                } else {
                    return \App\Response\Responses::failedToSelectModelForUpdateOrDelete();
                }

                $item_transfer = new ItemTransfer([
                    'resource_type_id' => $resource_type_id,
                    'from' => (int)$resource_id,
                    'to' => $new_resource_id,
                    'item_id' => $item_id,
                    'transferred_by' => $user_id
                ]);
                $item_transfer->save();
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (QueryException $e) {
            return \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return \App\Response\Responses::failedToSaveModelForUpdate();
        }

        return \App\Response\Responses::successNoContent();
    }
}
