<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Models\ResourceTypeItemType;
use App\Response\Cache;
use App\Request\Route;
use App\Models\ResourceType;
use App\Models\Transformers\ResourceType as ResourceTypeTransformer;
use App\Request\Validate\ResourceType as ResourceTypeValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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

        $cache_control = new Cache\Control($this->user_id);
        $cache_key = new Cache\Key();

        try {
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
                \App\Response\Responses::unableToDecode();
            }

            $resource_type_item_type = new ResourceTypeItemType([
                'resource_type_id' => $resource_type->id,
                'item_type_id' => $item_type_id
            ]);
            $resource_type_item_type->save();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->resourcesTypes()
            ]);

            if (request()->input('public', 0) !== 0) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->resourcesTypes()
                ]);
            }
        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForCreate();
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_key = new Cache\Key();

        $resource_type_item_type = (new ResourceTypeItemType())->instance($resource_type_id);
        $permitted_user = (new PermittedUser())->instance($resource_type_id, $this->user_id);
        $resource_type = (new ResourceType())->find($resource_type_id);

        $categories = (new Category())->total(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public
        );

        $resources = (new Resource())->totalCount(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public
        );

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

                $cache_control->clearPrivateCacheKeys([
                    $cache_key->resourcesTypes(),
                    $cache_key->permittedUsers($resource_type_id)
                ]);

                if (in_array((int) $resource_type_id, $this->public_resource_types, true)) {
                    $cache_control->clearPublicCacheKeys([
                        $cache_key->resourcesTypes(),
                        $cache_key->permittedUsers($resource_type_id)
                    ]);
                }

                \App\Response\Responses::successNoContent();
            } catch (QueryException $e) {
                \App\Response\Responses::foreignKeyConstraintError();
            } catch (Exception $e) {
                \App\Response\Responses::notFound(trans('entities.resource-type'));
            }
        } else {
            \App\Response\Responses::foreignKeyConstraintError();
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_key = new Cache\Key();

        $resource_type = (new ResourceType())->instance($resource_type_id);

        if ($resource_type === null) {
            \App\Response\Responses::failedToSelectModelForUpdateOrDelete();
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

        try {
            $resource_type->save();
            $cache_control->clearPrivateCacheKeys([
                $cache_key->resourcesTypes()
            ]);

            if (in_array((int) $resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->resourcesTypes()
                ]);
            }
        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForUpdate();
        }

        \App\Response\Responses::successNoContent();
    }
}
