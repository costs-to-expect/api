<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\ItemType\Select;
use App\Jobs\ClearCache;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Transformer\ItemSubcategory as ItemSubcategoryTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubcategoryController extends Controller
{
    public function create(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        if ($item_category_id === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->createItemSubcategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id, 1),
            'game' => \App\HttpResponse\Response::subcategoryAssignmentLimit(0),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function createItemSubcategory(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $assignment_limit = 1
    ): JsonResponse {
        $assigned = (new ItemSubcategory())->numberAssigned(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($assigned >= $assignment_limit) {
            return \App\HttpResponse\Response::subcategoryAssignmentLimit($assignment_limit);
        }

        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_category_id);

        $decode = $this->hash->subcategory()->decode(request()->input('subcategory_id'));
        $subcategory_id = null;
        if (count($decode) === 1) {
            $subcategory_id = $decode[0];
        }

        $messages = [];
        foreach (Config::get('api.item-subcategory.validation-post.messages') as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            ['subcategory_id' => $subcategory_id],
            [
                ...[
                    'subcategory_id' => [
                        'required'
                    ],
                ],
                ...Config::get('api.item-subcategory.validation-post.fields')
            ],
            $messages
        );

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors(
                $validator,
                (new \App\Models\AllowedValue\Subcategory())->allowedValues($item_category->category_id)
            );
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_SUBCATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        try {
            $subcategory_id = $this->hash->decode('subcategory', request()->input('subcategory_id'));

            if ($subcategory_id === false) {
                return \App\HttpResponse\Response::unableToDecode();
            }

            $item_sub_category = new ItemSubcategory([
                'item_category_id' => $item_category_id,
                'sub_category_id' => $subcategory_id
            ]);
            $item_sub_category->save();

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return \App\HttpResponse\Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new ItemSubcategoryTransformer((new ItemSubcategory())->instanceToArray($item_sub_category)))->asArray(),
            201
        );
    }

    public function delete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null,
        string $item_subcategory_id = null
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-subcategory'));
        }

        if ($item_category_id === null || $item_subcategory_id === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->deleteItemSubcategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id, (int) $item_subcategory_id),
            'game' => \App\HttpResponse\Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function deleteItemSubcategory(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $item_subcategory_id
    ): JsonResponse {
        $item_sub_category = (new ItemSubcategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'));
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_SUBCATEGORY_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        try {
            $item_sub_category->delete();

            ClearCache::dispatchSync($cache_job_payload->payload());

            return \App\HttpResponse\Response::successNoContent();
        } catch (QueryException $e) {
            return \App\HttpResponse\Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'), $e);
        }
    }
}
