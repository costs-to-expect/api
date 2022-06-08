<?php

namespace App\Http\Controllers;

use App\HttpResponse\Responses;
use App\Jobs\ClearCache;
use App\Models\Resource;
use App\Models\ResourceItemSubtype;
use App\Models\ResourceType;
use App\HttpRequest\Validate\Resource as ResourceValidator;
use App\Transformer\Resource as ResourceTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceManage extends Controller
{
    /**
     * Create a new resource
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function create(string $resource_type_id): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $validator = (new ResourceValidator)->create([
            'resource_type_id' => $resource_type_id,
            'item_type_id' => $resource_type['resource_type_item_type_id']
        ]);

        if ($validator->fails()) {
            return \App\HttpResponse\Responses::validationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->isPermittedUser($this->hasWriteAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $resource = DB::transaction(function() use ($resource_type_id) {
                $resource = new Resource([
                    'resource_type_id' => $resource_type_id,
                    'name' => request()->input('name'),
                    'description' => request()->input('description'),
                    'data' => request()->input('data')
                ]);
                $resource->save();

                $item_subtype_id = $this->hash->decode('item-subtype', request()->input('item_subtype_id'));

                if ($item_subtype_id === false) {
                    return \App\HttpResponse\Responses::unableToDecode();
                }

                $resource_item_subtype = new ResourceItemSubtype([
                    'resource_id' => $resource->id,
                    'item_subtype_id' => $item_subtype_id
                ]);
                $resource_item_subtype->save();

                return $resource;
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ResourceTransformer((New Resource())->instanceToArray($resource)))->asArray(),
            201
        );
    }

    /**
     * Delete the requested resource
     *
     * @param string $resource_type_id,
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $resource = (new Resource())->instance($resource_type_id, $resource_id);
        $resource_item_subtype = (new ResourceItemSubtype())->instance($resource_id);

        if ($resource === null || $resource_item_subtype === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->isPermittedUser($this->hasWriteAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            DB::transaction(function() use ($resource, $resource_item_subtype) {
                $resource_item_subtype->delete();
                $resource->delete();
            });

            ClearCache::dispatch($cache_job_payload->payload());

            return Responses::successNoContent();
        } catch (QueryException $e) {
            return Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return Responses::notFound(trans('entities.resource'));
        }
    }

    /**
     * Update the selected resource
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function update(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $resource = (new Resource())->instance($resource_type_id, $resource_id);

        if ($resource === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        if (count(request()->all()) === 0) {
            return \App\HttpResponse\Responses::nothingToPatch();
        }

        $validator = (new ResourceValidator())->update([
            'resource_type_id' => (int)$resource_type_id,
            'resource_id' => (int)$resource_id
        ]);

        if ($validator->fails()) {
            return \App\HttpResponse\Responses::validationErrors($validator);
        }

        $invalid_fields = $this->checkForInvalidFields(
            array_merge(
                (new Resource())->patchableFields(),
                (new ResourceValidator())->dynamicDefinedFields()
            )
        );

        if (count($invalid_fields) > 0) {
            return Responses::invalidFieldsInRequest($invalid_fields);
        }

        foreach (request()->all() as $key => $value) {
            $resource->$key = $value;
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_UPDATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->isPermittedUser($this->hasWriteAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $resource->save();

            ClearCache::dispatch($cache_job_payload->payload());
            
        } catch (Exception $e) {
            return Responses::failedToSaveModelForUpdate();
        }

        return Responses::successNoContent();
    }
}
