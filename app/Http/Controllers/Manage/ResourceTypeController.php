<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\HttpResponse\Response;
use App\Jobs\ClearCache;
use App\Models\Category;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\ResourceTypeItemType;
use App\HttpRequest\Validate\ResourceType as ResourceTypeValidator;
use App\Transformer\ResourceType as ResourceTypeTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeController extends Controller
{
    protected bool $allow_entire_collection = true;

    public function create(Request $request): JsonResponse
    {
        $validator = (new ResourceTypeValidator())->create([
            'user_id' => $this->user_id
        ]);

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_CREATE)
            ->setRouteParameters([])
            ->setUserId($this->user_id);

        try {
            $resource_type = DB::transaction(function () use ($request) {
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
                    return \App\HttpResponse\Response::unableToDecode();
                }

                $resource_type_item_type = new ResourceTypeItemType([
                    'resource_type_id' => $resource_type->id,
                    'item_type_id' => $item_type_id
                ]);
                $resource_type_item_type->save();

                return $resource_type;
            });

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return \App\HttpResponse\Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new ResourceTypeTransformer((new ResourceType())->instanceToArray($resource_type)))->asArray(),
            201
        );
    }

    public function delete(
        Request $request,
        string $resource_type_id
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type_item_type = (new ResourceTypeItemType())->instance($resource_type_id);
        $permitted_user = (new PermittedUser())->instanceByUserId($resource_type_id, $this->user_id);
        $resource_type = (new ResourceType())->find($resource_type_id);

        $categories = (new Category())->total(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $resources = (new Resource())->totalCount(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setUserId($this->user_id);

        if (
            $categories === 0 &&
            $resources === 0 &&
            $resource_type_item_type !== null &&
            $permitted_user !== null &&
            $resource_type !== null
        ) {
            try {
                DB::transaction(static function () use ($resource_type_item_type, $permitted_user, $resource_type) {
                    $resource_type_item_type->delete();
                    $permitted_user->delete();
                    $resource_type->delete();
                });

                ClearCache::dispatchSync($cache_job_payload->payload());

                return \App\HttpResponse\Response::successNoContent();
            } catch (QueryException $e) {
                return \App\HttpResponse\Response::foreignKeyConstraintError($e);
            } catch (Exception $e) {
                return \App\HttpResponse\Response::notFound(trans('entities.resource-type'), $e);
            }
        } else {
            return \App\HttpResponse\Response::foreignKeyConstraintError();
        }
    }

    public function update(
        Request $request,
        string $resource_type_id
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type = (new ResourceType())->instance($resource_type_id);

        if ($resource_type === null) {
            return \App\HttpResponse\Response::failedToSelectModelForUpdateOrDelete();
        }

        if (count($request->all()) === 0) {
            return \App\HttpResponse\Response::nothingToPatch();
        }

        $validator = (new ResourceTypeValidator())->update([
            'resource_type_id' => (int) $resource_type_id,
            'user_id' => $this->user_id
        ]);

        if ($validator !== null && $validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $invalid_fields = $this->checkForInvalidFields(
            [
                ...(new ResourceType())->patchableFields(),
                ...(new ResourceTypeValidator())->dynamicDefinedFields()
            ]
        );

        if (count($invalid_fields) > 0) {
            return Response::invalidFieldsInRequest($invalid_fields);
        }

        foreach ($request->all() as $key => $value) {
            $resource_type->$key = $value;
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_UPDATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setUserId($this->user_id);

        try {
            $resource_type->save();

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return \App\HttpResponse\Response::failedToSaveModelForUpdate($e);
        }

        return \App\HttpResponse\Response::successNoContent();
    }
}
