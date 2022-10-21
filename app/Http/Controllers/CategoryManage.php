<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use Illuminate\Http\Request;
use App\HttpResponse\Response;
use App\Jobs\ClearCache;
use App\Models\Category;
use App\HttpRequest\Validate\Category as CategoryValidator;
use App\Transformer\Category as CategoryTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryManage extends Controller
{
    public function create(Request $request, $resource_type_id): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type = (new ResourceType())->single($resource_type_id, $this->permitted_resource_types);

        $validator = (new CategoryValidator())->create([
            'resource_type_id' => $resource_type_id,
            'item_type' => $resource_type['resource_type_item_type_name']
        ]);
        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::CATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setUserId($this->user_id);

        try {
            $category = new Category([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'resource_type_id' => $resource_type_id
            ]);
            $category->save();

            ClearCache::dispatch($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new CategoryTransformer((new Category())->instanceToArray($category)))->asArray(),
            201
        );
    }

    /**
     * Delete the requested category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function delete(
        $resource_type_id,
        $category_id
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::CATEGORY_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setUserId($this->user_id);

        $category = (new Category())->find($category_id);
        if ($category === null) {
            return Response::notFound(trans('entities.category'));
        }

        try {
            $category->delete();

            ClearCache::dispatch($cache_job_payload->payload());

            return Response::successNoContent();
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::notFound(trans('entities.category'), $e);
        }
    }

    /**
     * Update the selected category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $resource_type_id, $category_id): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        $category = (new Category())->instance($category_id);

        if ($category === null) {
            return Response::failedToSelectModelForUpdateOrDelete();
        }

        if (count($request->all()) === 0) {
            return \App\HttpResponse\Response::nothingToPatch();
        }

        $validator = (new CategoryValidator())->update([
            'resource_type_id' => $category->resource_type_id,
            'category_id' => $category->id
        ]);

        if ($validator === null) {
            return Response::failedToSelectModelForUpdateOrDelete();
        }

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $invalid_fields = $this->checkForInvalidFields(
            [
                ...(new Category())->patchableFields(),
                ...(new CategoryValidator())->dynamicDefinedFields()
            ]
        );

        if (count($invalid_fields) > 0) {
            return Response::invalidFieldsInRequest($invalid_fields);
        }

        foreach ($request->all() as $key => $value) {
            $category->$key = $value;
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::CATEGORY_UPDATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setUserId($this->user_id);

        try {
            $category->save();

            ClearCache::dispatch($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForUpdate($e);
        }

        return Response::successNoContent();
    }
}
