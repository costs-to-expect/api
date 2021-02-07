<?php

namespace App\Http\Controllers;

use App\ItemType\Entity;
use App\Jobs\ClearCache;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Transformers\ItemSubcategory as ItemSubcategoryTransformer;
use App\Request\Validate\ItemSubcategory as ItemSubcategoryValidator;
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
class ItemSubcategoryManage extends Controller
{
    /**
     * Assign the sub category
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function create(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        if ($item_category_id === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $entity = Entity::item($resource_type_id);
        $assigned = (new ItemSubcategory())->numberAssigned(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($assigned >= $entity->subcategoryAssignmentLimit()) {
            return \App\Response\Responses::subcategoryAssignmentLimit($entity->subcategoryAssignmentLimit());
        }

        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_category_id);

        $validator = (new ItemSubcategoryValidator)->create(['category_id' => $item_category->category_id]);

        if ($validator->fails()) {
            \App\Request\BodyValidation::returnValidationErrors(
                $validator,
                (new \App\AllowedValue\Subcategory())->allowedValues($item_category->category_id)
            );
        }

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::ITEM_SUBCATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $subcategory_id = $this->hash->decode('subcategory', request()->input('subcategory_id'));

            if ($subcategory_id === false) {
                return \App\Response\Responses::unableToDecode();
            }

            $item_sub_category = new ItemSubcategory([
                'item_category_id' => $item_category_id,
                'sub_category_id' => $subcategory_id
            ]);
            $item_sub_category->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemSubcategoryTransformer((new ItemSubcategory())->instanceToArray($item_sub_category)))->asArray(),
            201
        );
    }

    /**
     * Delete the assigned sub category
     *
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id,
     * @param string $item_subcategory_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null,
        string $item_subcategory_id = null
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-subcategory'));
        }

        if ($item_category_id === null || $item_subcategory_id === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::ITEM_SUBCATEGORY_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $item_sub_category->delete();

            ClearCache::dispatchNow($cache_job_payload->payload());

            return \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            return \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }
    }
}
