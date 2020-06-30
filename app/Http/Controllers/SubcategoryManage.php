<?php

namespace App\Http\Controllers;

use App\Response\Cache;
use App\Request\Route;
use App\Models\Subcategory;
use App\Models\Transformers\Subcategory as SubcategoryTransformer;
use App\Request\Validate\Subcategory as SubcategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $validator = (new SubcategoryValidator)->create(['category_id' => $category_id]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        try {
            $sub_category = new Subcategory([
                'category_id' => $category_id,
                'name' => request()->input('name'),
                'description' => request()->input('description')
            ]);
            $sub_category->save();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->categories($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->categories($resource_type_id)
                ]);
            }
        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForCreate();
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
        Route\Validate::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $sub_category = (new Subcategory())->instance(
            $category_id,
            $subcategory_id
        );

        if ($sub_category === null) {
            \App\Response\Responses::notFound(trans('entities.subcategory'));
        }

        try {
            $sub_category->delete();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->categories($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->categories($resource_type_id)
                ]);
            }

            \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            \App\Response\Responses::notFound(trans('entities.subcategory'), $e);
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
        Route\Validate::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $subcategory = (new Subcategory())->instance($category_id, $subcategory_id);

        if ($subcategory === null) {
            \App\Response\Responses::failedToSelectModelForUpdateOrDelete();
        }

        \App\Request\BodyValidation::checkForEmptyPatch();

        $validator = (new SubcategoryValidator())->update([
            'category_id' => (int)$category_id,
            'subcategory_id' => (int)$subcategory_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        \App\Request\BodyValidation::checkForInvalidFields(
            array_merge(
                (new Subcategory())->patchableFields(),
                (new SubcategoryValidator)->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $subcategory->$key = $value;
        }

        try {
            $subcategory->save();

            $cache_control->clearPrivateCacheKeys([
                // We need to clear subcategories, resource type items
                // and items dur to includes so simpler to clear the entire
                // resource type
                $cache_key->resourceType($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->resourceType($resource_type_id)
                ]);
            }
        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForUpdate();
        }

        \App\Response\Responses::successNoContent();
    }
}
