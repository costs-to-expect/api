<?php

namespace App\Http\Controllers;

use App\Jobs\ClearCache;
use App\Models\Category;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\ResourceTypeItemType;
use App\Request\Validate\ResourceType as ResourceTypeValidator;
use App\Response\Responses;
use App\Transformers\ResourceType as ResourceTypeTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeManage extends Controller
{
    protected bool $allow_entire_collection = true;

    public function create(Request $request): JsonResponse
    {
        $validator = (new ResourceTypeValidator)->create([
            'user_id' => $this->user_id
        ]);

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_CREATE)
            ->setRouteParameters([])
            ->setPermittedUser(true)
            ->setUserId($this->user_id);

        try {
            $resource_type = DB::transaction(function() use($request) {
                $resource_type = new ResourceType([
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'data' => $request->input('data'),
                    'public' => $request->input('public', 0)
                ]);
                $resource_type->save();

                $permitted_users = new PermittedUser([
                    'resource_type_id' => $resource_type->id,
                    'user_id' => $this->user_id,
                    'added_by' => $this->user_id
                ]);
                $permitted_users->save();

                $item_type_id = $this->hash->decode('item-type', $request->input('item_type_id'));

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

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ResourceTypeTransformer((New ResourceType())->instanceToArray($resource_type)))->asArray(),
            201
        );
    }

    public function delete(
        Request $request,
        string $resource_type_id
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type_item_type = (new ResourceTypeItemType())->instance($resource_type_id);
        $permitted_user = (new PermittedUser())->instanceByUserId($resource_type_id, $this->user_id);
        $resource_type = (new ResourceType())->find($resource_type_id);

        $categories = (new Category())->total(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $resources = (new Resource())->totalCount(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_DELETE)
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

                ClearCache::dispatch($cache_job_payload->payload());

                return \App\Response\Responses::successNoContent();
            } catch (QueryException $e) {
                Log::error($e->getMessage());
                return \App\Response\Responses::foreignKeyConstraintError();
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return \App\Response\Responses::notFound(trans('entities.resource-type'));
            }
        } else {
            return \App\Response\Responses::foreignKeyConstraintError();
        }
    }

    public function update(
        Request $request,
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

        if (count($request->all()) === 0) {
            return \App\Response\Responses::nothingToPatch();
        }

        $validator = (new ResourceTypeValidator())->update([
            'resource_type_id' => (int) $resource_type_id,
            'user_id' => $this->user_id
        ]);

        if ($validator !== null && $validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $invalid_fields = \App\Request\BodyValidation::checkForInvalidFields(
            array_merge(
                (new ResourceType())->patchableFields(),
                (new ResourceTypeValidator())->dynamicDefinedFields()
            )
        );

        if (count($invalid_fields) > 0) {
            return Responses::invalidFieldsInRequest($invalid_fields);
        }

        foreach ($request->all() as $key => $value) {
            $resource_type->$key = $value;
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_UPDATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $resource_type->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return \App\Response\Responses::failedToSaveModelForUpdate();
        }

        return \App\Response\Responses::successNoContent();
    }
}
