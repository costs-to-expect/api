<?php

namespace App\Http\Controllers;

use App\Entity\Item\Entity;
use App\Jobs\ClearCache;
use App\Response\Cache;
use App\Request\Route;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Models\Transformers\ItemSubcategory as ItemSubcategoryTransformer;
use App\Request\Validate\ItemSubcategory as ItemSubcategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
        string $item_category_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        if ($item_category_id === 'nill') {
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
        \App\Request\BodyValidation::validateAndReturnErrors(
            $validator,
            (new \App\Option\AllowedValue\Subcategory())->allowedValues($item_category->category_id)
        );

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::ITEM_SUBCATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser(in_array((int) $resource_type_id, $this->permitted_resource_types, true))
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
        string $item_category_id,
        string $item_subcategory_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        if ($item_category_id === 'nill' || $item_subcategory_id === 'nill') {
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
            ->setPermittedUser(in_array((int) $resource_type_id, $this->permitted_resource_types, true))
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
