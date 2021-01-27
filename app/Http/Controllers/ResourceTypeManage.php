<?php

namespace App\Http\Controllers;

use App\Jobs\ClearCache;
use App\Models\Category;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Models\ResourceTypeItemType;
use App\Response\Cache;
use App\Models\ResourceType;
use App\Transformers\ResourceType as ResourceTypeTransformer;
use App\Request\Validate\ResourceType as ResourceTypeValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeManage extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Create a new resource type
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $validator = (new ResourceTypeValidator)->create([
            'user_id' => $this->user_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::RESOURCE_TYPE_CREATE)
            ->setRouteParameters([])
            ->setPermittedUser(true)
            ->setUserId($this->user_id);

        try {
            $resource_type = DB::transaction(function() {
                $resource_type = new ResourceType([
                    'name' => request()->input('name'),
                    'description' => request()->input('description'),
                    'public' => request()->input('public', 0)
                ]);
                $resource_type->save();

                $permitted_users = new PermittedUser([
                    'resource_type_id' => $resource_type->id,
                    'user_id' => $this->user_id,
                    'added_by' => $this->user_id
                ]);
                $permitted_users->save();

                $item_type_id = $this->hash->decode('item-type', request()->input('item_type_id'));

                if ($item_type_id === false) {
                    return \App\Response\Responses::unableToDecode();
                }

                $resource_type_item_type = new ResourceTypeItemType([
                    'resource_type_id' => $resource_type->id,
                    'item_type_id' => $item_type_id
                ]);
                $resource_type_item_type->save();

                return $resource_type;
            });

            ClearCache::dispatchNow($cache_job_payload->payload());

        } catch (Exception $e) {
            return \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ResourceTypeTransformer((New ResourceType())->instanceToArray($resource_type)))->asArray(),
            201
        );
    }

    /**
     * Delete the requested resource type
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type_item_type = (new ResourceTypeItemType())->instance($resource_type_id);
        $permitted_user = (new PermittedUser())->instance($resource_type_id, $this->user_id);
        $resource_type = (new ResourceType())->find($resource_type_id);

        $categories = (new Category())->total(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $resources = (new Resource())->totalCount(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::RESOURCE_TYPE_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        if (
            $categories === 0 &&
            $resources === 0 &&
            $resource_type_item_type !== null &&
            $permitted_user !== null &&
            $resource_type !== null
        ) {
            try {
                DB::transaction(static function() use ($resource_type_item_type, $permitted_user, $resource_type) {
                    $resource_type_item_type->delete();
                    $permitted_user->delete();
                    $resource_type->delete();
                });

                ClearCache::dispatchNow($cache_job_payload->payload());

                return \App\Response\Responses::successNoContent();
            } catch (QueryException $e) {
                return \App\Response\Responses::foreignKeyConstraintError();
            } catch (Exception $e) {
                return \App\Response\Responses::notFound(trans('entities.resource-type'));
            }
        } else {
            return \App\Response\Responses::foreignKeyConstraintError();
        }
    }

    /**
     * Update the selected category
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function update(
        string $resource_type_id
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type = (new ResourceType())->instance($resource_type_id);

        if ($resource_type === null) {
            return \App\Response\Responses::failedToSelectModelForUpdateOrDelete();
        }

        \App\Request\BodyValidation::checkForEmptyPatch();

        $validator = (new ResourceTypeValidator())->update([
            'resource_type_id' => (int) ($resource_type_id),
            'user_id' => $this->user_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        \App\Request\BodyValidation::checkForInvalidFields(
            array_merge(
                (new ResourceType())->patchableFields(),
                (new ResourceTypeValidator())->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $resource_type->$key = $value;
        }

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::RESOURCE_TYPE_UPDATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $resource_type->save();

            ClearCache::dispatch($cache_job_payload->payload())->delay(now()->addMinute());

        } catch (Exception $e) {
            return \App\Response\Responses::failedToSaveModelForUpdate();
        }

        return \App\Response\Responses::successNoContent();
    }
}
