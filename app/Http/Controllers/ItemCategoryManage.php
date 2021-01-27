<?php

namespace App\Http\Controllers;

use App\ItemType\Entity;
use App\Jobs\ClearCache;
use App\Models\ItemCategory;
use App\Transformers\ItemCategory as ItemCategoryTransformer;
use App\Request\Validate\ItemCategory as ItemCategoryValidator;
use App\Response\Cache;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategoryManage extends Controller
{
    /**
     * Assign the category
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function create(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $entity = Entity::item($resource_type_id);
        $assigned = (new ItemCategory())->numberAssigned(
            $resource_type_id,
            $resource_id,
            $item_id
        );

        if ($assigned >= $entity->categoryAssignmentLimit()) {
            return \App\Response\Responses::categoryAssignmentLimit($entity->categoryAssignmentLimit());
        }

        $validator = (new ItemCategoryValidator)->create();
        \App\Request\BodyValidation::validateAndReturnErrors(
            $validator,
            (new \App\AllowedValue\Category())->allowedValues($resource_type_id)
        );

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::ITEM_CATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $category_id = $this->hash->decode('category', request()->input('category_id'));

            if ($category_id === false) {
                return \App\Response\Responses::unableToDecode();
            }

            $item_category = new ItemCategory([
                'item_id' => $item_id,
                'category_id' => $category_id
            ]);
            $item_category->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemCategoryTransformer((new ItemCategory())->instanceToArray($item_category)))->asArray(),
            201
        );
    }

    /**
     * Delete the assigned category
     *
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        $item_category = (new ItemCategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            return \App\Response\Responses::notFound(trans('entities.item-category'));
        }

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::ITEM_CATEGORY_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $item_category->delete();

            ClearCache::dispatchNow($cache_job_payload->payload());

            return \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            return \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return \App\Response\Responses::notFound(trans('entities.item-category'));
        }
    }
}
