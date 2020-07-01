<?php

namespace App\Http\Controllers;

Use App\Response\Cache;
use App\Request\Route;
use App\Models\Category;
use App\Models\Transformers\Category as CategoryTransformer;
use App\Request\Validate\Category as CategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
        Route\Validate::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $validator = (new CategoryValidator)->create([
            'resource_type_id' => $resource_type_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        try {
            $category = new Category([
                'name' => request()->input('name'),
                'description' => request()->input('description'),
                'resource_type_id' => $resource_type_id
            ]);
            $category->save();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->categories($resource_type_id)
            ]);

            if (in_array((int) $resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->categories($resource_type_id)
                ]);
            }
        } catch (Exception $e) {
           \App\Response\Responses::failedToSaveModelForCreate();
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
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        try {
            (new Category())->find($category_id)->delete();
            $cache_control->clearPrivateCacheKeys([
                $cache_key->categories($resource_type_id)
            ]);

            if (in_array((int) $resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->categories($resource_type_id)
                ]);
            }

            \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            \App\Response\Responses::notFound(trans('entities.category'), $e);
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
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $category = (new Category())->instance($category_id);

        if ($category === null) {
            \App\Response\Responses::failedToSelectModelForUpdateOrDelete();
        }

        \App\Request\BodyValidation::checkForEmptyPatch();

        $validator = (new CategoryValidator)->update([
            'resource_type_id' => (int)$category->resource_type_id,
            'category_id' => (int)$category_id
        ]);
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        \App\Request\BodyValidation::checkForInvalidFields(
            array_merge(
                (new Category())->patchableFields(),
                (new CategoryValidator)->dynamicDefinedFields()
            )
        );

        foreach (request()->all() as $key => $value) {
            $category->$key = $value;
        }

        try {
            $category->save();

            $cache_control->clearPrivateCacheKeys([
                // We need to clear categories, resource type items
                // and items due to includes so simpler to clear the entire
                // resource type
                $cache_key->resourceType($resource_type_id)
            ]);

            if (in_array((int) $resource_type_id, $this->public_resource_types, true)) {
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
