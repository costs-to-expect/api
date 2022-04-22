<?php

namespace App\Http\Controllers;

use App\HttpResponse\Responses;
use App\Jobs\ClearCache;
use App\Models\Category;
use App\HttpRequest\BodyValidation;
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
    /**
     * Create a new category
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function create($resource_type_id): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $validator = (new CategoryValidator)->create([
            'resource_type_id' => $resource_type_id
        ]);
        if ($validator->fails()) {
            return \App\HttpRequest\BodyValidation::returnValidationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::CATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $category = new Category([
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'resource_type_id' => $resource_type_id
            ]);
            $category->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
           return Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new CategoryTransformer((new Category)->instanceToArray($category)))->asArray(),
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
    ): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::CATEGORY_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        $category = (new Category())->find($category_id);
        if ($category === null) {
            return Responses::notFound(trans('entities.category'));
        }

        try {
            $category->delete();

            ClearCache::dispatch($cache_job_payload->payload());

            return Responses::successNoContent();
        } catch (QueryException $e) {
            return Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return Responses::notFound(trans('entities.category'));
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
    public function update($resource_type_id, $category_id): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        $category = (new Category())->instance($category_id);

        if ($category === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        if (count(request()->all()) === 0) {
            return \App\HttpResponse\Responses::nothingToPatch();
        }

        $validator = (new CategoryValidator)->update([
            'resource_type_id' => (int) $category->resource_type_id,
            'category_id' => (int) $category_id
        ]);

        if ($validator === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        if ($validator->fails()) {
            return \App\HttpRequest\BodyValidation::returnValidationErrors($validator);
        }

        $invalid_fields = BodyValidation::checkForInvalidFields(
            array_merge(
                (new Category())->patchableFields(),
                (new CategoryValidator)->dynamicDefinedFields()
            )
        );

        if (count($invalid_fields) > 0) {
            return Responses::invalidFieldsInRequest($invalid_fields);
        }

        foreach (request()->all() as $key => $value) {
            $category->$key = $value;
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::CATEGORY_UPDATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $category->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForUpdate();
        }

        return Responses::successNoContent();
    }
}
