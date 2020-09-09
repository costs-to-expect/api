<?php

namespace App\Http\Controllers;

use App\Response\Cache;
use App\Request\Route;
use App\Models\Resource;
use App\Models\Transformers\Resource as ResourceTransformer;
use App\Request\Validate\Resource as ResourceValidator;
use App\Response\Responses;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

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

        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_key = new Cache\Key();

        $validator = (new ResourceValidator)->create(['resource_type_id' => $resource_type_id]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        try {
            $resource = new Resource([
                'resource_type_id' => $resource_type_id,
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'effective_date' => request()->input('effective_date')
            ]);
            $resource->save();

            $cache_trash = new Cache\Trash(
                $cache_control,
                [
                    $cache_key->resourceType($resource_type_id)
                ],
                $resource_type_id,
                $this->public_resource_types,
                $this->permittedUsers($resource_type_id)
            );
            $cache_trash->all();

        } catch (Exception $e) {
            Responses::failedToSaveModelForCreate();
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

        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_key = new Cache\Key();

        $resource = (new Resource())->find($resource_id);

        if ($resource === null) {
            Responses::failedToSelectModelForUpdateOrDelete();
        }

        try {
            (new Resource())->find($resource_id)->delete();

            $cache_trash = new Cache\Trash(
                $cache_control,
                [
                    $cache_key->resourceType($resource_type_id)
                ],
                $resource_type_id,
                $this->public_resource_types,
                $this->permittedUsers($resource_type_id)
            );
            $cache_trash->all();

            Responses::successNoContent();
        } catch (QueryException $e) {
            Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            Responses::notFound(trans('entities.resource'));
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

        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_key = new Cache\Key();

        $resource = (new Resource())->instance($resource_type_id, $resource_id);

        if ($resource === null) {
            Responses::failedToSelectModelForUpdateOrDelete();
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

        try {
            $resource->save();

            $cache_trash = new Cache\Trash(
                $cache_control,
                [
                    $cache_key->resourceType($resource_type_id)
                ],
                $resource_type_id,
                $this->public_resource_types,
                $this->permittedUsers($resource_type_id)
            );
            $cache_trash->all();
            
        } catch (Exception $e) {
            Responses::failedToSaveModelForUpdate();
        }

        Responses::successNoContent();
    }
}
