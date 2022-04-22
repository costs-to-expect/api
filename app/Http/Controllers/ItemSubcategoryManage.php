<?php

namespace App\Http\Controllers;

use App\ItemType\Entity;
use App\Jobs\ClearCache;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Transformers\ItemSubcategory as ItemSubcategoryTransformer;
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
class ItemSubcategoryManage extends Controller
{
    public function create(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        if ($item_category_id === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'simple-expense' => $this->createItemSubcategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id, 1),
            'game', 'simple-item' => \App\HttpResponse\Responses::subcategoryAssignmentLimit(0),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function createItemSubcategory(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $assignment_limit = 1
    ): JsonResponse
    {
        $assigned = (new ItemSubcategory())->numberAssigned(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($assigned >= $assignment_limit) {
            return \App\HttpResponse\Responses::subcategoryAssignmentLimit($assignment_limit);
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
            array_merge(
                [
                    'subcategory_id' => [
                        'required'
                    ],
                ],
                Config::get('api.item-subcategory.validation-post.fields')
            ),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors(
                $validator,
                (new \App\AllowedValue\Subcategory())->allowedValues($item_category->category_id)
            );
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_SUBCATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $subcategory_id = $this->hash->decode('subcategory', request()->input('subcategory_id'));

            if ($subcategory_id === false) {
                return \App\HttpResponse\Responses::unableToDecode();
            }

            $item_sub_category = new ItemSubcategory([
                'item_category_id' => $item_category_id,
                'sub_category_id' => $subcategory_id
            ]);
            $item_sub_category->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return \App\HttpResponse\Responses::failedToSaveModelForCreate();
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
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item-subcategory'));
        }

        if ($item_category_id === null || $item_subcategory_id === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'simple-expense' => $this->deleteItemSubcategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id, (int) $item_subcategory_id),
            'game', 'simple-item' => \App\HttpResponse\Responses::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function deleteItemSubcategory(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $item_subcategory_id
    ): JsonResponse
    {
        $item_sub_category = (new ItemSubcategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.item-subcategory'));
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::ITEM_SUBCATEGORY_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $item_sub_category->delete();

            ClearCache::dispatch($cache_job_payload->payload());

            return \App\HttpResponse\Responses::successNoContent();
        } catch (QueryException $e) {
            return \App\HttpResponse\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return \App\HttpResponse\Responses::notFound(trans('entities.item-subcategory'));
        }
    }
}
