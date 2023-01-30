<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\HttpResponse\Response;
use App\ItemType\Select;
use App\Jobs\ClearCache;
use App\Models\ItemCategory;
use App\Transformer\ItemCategory as ItemCategoryTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategoryController extends Controller
{
    public function create(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->createItemCategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, 1),
            'game' => $this->createItemCategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, 10),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function createItemCategory(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $assignment_limit = 1
    ): JsonResponse {
        $assigned = (new ItemCategory())->numberAssigned(
            $resource_type_id,
            $resource_id,
            $item_id
        );

        if ($assigned >= $assignment_limit) {
            return \App\HttpResponse\Response::categoryAssignmentLimit($assignment_limit);
        }

        $decode = $this->hash->category()->decode(request()->input('category_id'));
        $category_id = null;
        if (count($decode) === 1) {
            $category_id = $decode[0];
        }

        $messages = [];
        foreach (Config::get('api.item-category.validation-post.messages') as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            ['category_id' => $category_id],
            Config::get('api.item-category.validation-post.fields'),
            $messages
        );

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors(
                $validator,
                (new \App\Models\AllowedValue\Category())->allowedValues($resource_type_id)
            );
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_CATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        try {
            $category_id = $this->hash->decode('category', request()->input('category_id'));

            if ($category_id === false) {
                return \App\HttpResponse\Response::unableToDecode();
            }

            $item_category = new ItemCategory([
                'item_id' => $item_id,
                'category_id' => $category_id
            ]);
            $item_category->save();

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return \App\HttpResponse\Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new ItemCategoryTransformer((new ItemCategory())->instanceToArray($item_category)))->asArray(),
            201
        );
    }

    public function delete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'game' => $this->deleteItemCategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function deleteItemCategory(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id
    ): JsonResponse {
        $item_category = (new ItemCategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-category'));
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_CATEGORY_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        try {
            $item_category->delete();

            ClearCache::dispatchSync($cache_job_payload->payload());

            return \App\HttpResponse\Response::successNoContent();
        } catch (QueryException $e) {
            return \App\HttpResponse\Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-category'), $e);
        }
    }
}
