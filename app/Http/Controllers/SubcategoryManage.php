<?php

namespace App\Http\Controllers;

use App\HttpResponse\Response;
use App\Jobs\ClearCache;
use App\Models\Subcategory;
use App\HttpRequest\Validate\Subcategory as SubcategoryValidator;
use App\Transformer\Subcategory as SubcategoryTransformer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubcategoryManage extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Create a new sub category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function create($resource_type_id, $category_id): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.category'));
        }

        $validator = (new SubcategoryValidator)->create(['category_id' => $category_id]);

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::SUBCATEGORY_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'category_id' => $category_id
            ])
            ->isPermittedUser($this->hasWriteAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $sub_category = new Subcategory([
                'category_id' => $category_id,
                'name' => request()->input('name'),
                'description' => request()->input('description')
            ]);
            $sub_category->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new SubcategoryTransformer((new Subcategory())->instanceToArray($sub_category)))->asArray(),
            201
        );
    }

    /**
     * Delete the requested sub category
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function delete(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.subcategory'));
        }

        $sub_category = (new Subcategory())->instance(
            $category_id,
            $subcategory_id
        );

        if ($sub_category === null) {
            return Response::notFound(trans('entities.subcategory'));
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::SUBCATEGORY_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'category_id' => $category_id
            ])
            ->isPermittedUser($this->hasWriteAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $sub_category->delete();

            ClearCache::dispatch($cache_job_payload->payload());

            return Response::successNoContent();
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::notFound(trans('entities.subcategory'), $e);
        }
    }

    /**
     * Update the selected subcategory
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function update(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.subcategory'));
        }

        $subcategory = (new Subcategory())->instance($category_id, $subcategory_id);

        if ($subcategory === null) {
            return Response::failedToSelectModelForUpdateOrDelete();
        }

        if (count(request()->all()) === 0) {
            return \App\HttpResponse\Response::nothingToPatch();
        }

        $validator = (new SubcategoryValidator())->update([
            'category_id' => (int)$category_id,
            'subcategory_id' => (int)$subcategory_id
        ]);

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $invalid_fields = $this->checkForInvalidFields(
            array_merge(
                (new Subcategory())->patchableFields(),
                (new SubcategoryValidator)->dynamicDefinedFields()
            )
        );

        if (count($invalid_fields) > 0) {
            return Response::invalidFieldsInRequest($invalid_fields);
        }

        foreach (request()->all() as $key => $value) {
            $subcategory->$key = $value;
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::SUBCATEGORY_UPDATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'category_id' => $category_id
            ])
            ->isPermittedUser($this->hasWriteAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $subcategory->save();

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Response::failedToSaveModelForUpdate($e);
        }

        return Response::successNoContent();
    }
}
