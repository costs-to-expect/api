<?php

namespace App\Http\Controllers;

use App\Jobs\ClearCache;
use App\Models\ResourceItemSubtype;
use App\Response\Cache;
use App\Request\Route;
use App\Models\Resource;
use App\Models\Transformers\Resource as ResourceTransformer;
use App\Request\Validate\Resource as ResourceValidator;
use App\Response\Responses;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Manage resources
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceManage extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Create a new resource
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function create(string $resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        $validator = (new ResourceValidator)->create([
            'resource_type_id' => $resource_type_id,
            'item_type_id' => 3
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::RESOURCE_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser(in_array((int) $resource_type_id, $this->permitted_resource_types, true))
            ->setUserId($this->user_id);

        try {
            $resource = DB::transaction(function() use ($resource_type_id) {
                $resource = new Resource([
                    'resource_type_id' => $resource_type_id,
                    'name' => request()->input('name'),
                    'description' => request()->input('description'),
                    'effective_date' => request()->input('effective_date')
                ]);
                $resource->save();

                $item_subtype_id = $this->hash->decode('item-subtype', request()->input('item_subtype_id'));

                if ($item_subtype_id === false) {
                    return \App\Response\Responses::unableToDecode();
                }

                $resource_item_subtype = new ResourceItemSubtype([
                    'resource_id' => $resource->id,
                    'item_subtype_id' => $item_subtype_id
                ]);
                $resource_item_subtype->save();

                return $resource;
            });

            ClearCache::dispatch($cache_job_payload->payload())->delay(now()->addMinute());

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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            true
        );

        $resource = (new Resource())->find($resource_id);

        if ($resource === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::RESOURCE_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser(in_array((int) $resource_type_id, $this->permitted_resource_types, true))
            ->setUserId($this->user_id);

        try {
            (new Resource())->find($resource_id)->delete();

            ClearCache::dispatch($cache_job_payload->payload())->delay(now()->addMinute());

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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            true
        );

        $resource = (new Resource())->instance($resource_type_id, $resource_id);

        if ($resource === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        \App\Request\BodyValidation::checkForEmptyPatch();

        $validator = (new ResourceValidator())->update([
            'resource_type_id' => (int)$resource_type_id,
            'resource_id' => (int)$resource_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        \App\Request\BodyValidation::checkForInvalidFields(
            array_merge(
                (new Resource())->patchableFields(),
                (new ResourceValidator())->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $resource->$key = $value;
        }

        $cache_job_payload = (new Cache\JobPayload())
            ->setGroupKey(Cache\KeyGroup::RESOURCE_UPDATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser(in_array((int) $resource_type_id, $this->permitted_resource_types, true))
            ->setUserId($this->user_id);

        try {
            $resource->save();

            ClearCache::dispatch($cache_job_payload->payload())->delay(now()->addMinute());
            
        } catch (Exception $e) {
            return Responses::failedToSaveModelForUpdate();
        }

        return Responses::successNoContent();
    }
}
