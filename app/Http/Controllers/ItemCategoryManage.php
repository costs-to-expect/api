<?php

namespace App\Http\Controllers;

use App\Response\Cache;
use App\Request\Route;
use App\Models\Category;
use App\Models\ItemCategory;
use App\Models\Transformers\ItemCategory as ItemCategoryTransformer;
use App\Request\Validate\ItemCategory as ItemCategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategoryManage extends Controller
{
    /**
     * Assign the category
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function create(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $validator = (new ItemCategoryValidator)->create();
        \App\Request\BodyValidation::validateAndReturnErrors(
            $validator,
            $this->fieldsData($resource_type_id)
        );

        try {
            $category_id = $this->hash->decode('category', request()->input('category_id'));

            if ($category_id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $item_category = new ItemCategory([
                'item_id' => $item_id,
                'category_id' => $category_id
            ]);
            $item_category->save();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->items($resource_type_id, $resource_id),
                $cache_key->resourceTypeItems($resource_type_id)
            ]);

            if (in_array((int) $resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->items($resource_type_id, $resource_id),
                    $cache_key->resourceTypeItems($resource_type_id)
                ]);
            }
        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemCategoryTransformer((new ItemCategory())->instanceToArray($item_category)))->asArray(),
            201
        );
    }

    /**
     * Generate any conditional POST parameters, will be merged with the relevant
     * config/api/[type]/fields.php data array
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function fieldsData($resource_type_id): array
    {
        $categories = (new Category())->categoriesByResourceType($resource_type_id);

        $conditional_post_parameters = ['category_id' => []];
        foreach ($categories as $category) {
            $id = $this->hash->encode('category', $category['category_id']);

            if ($id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $conditional_post_parameters['category_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $category['category_name'],
                'description' => $category['category_description']
            ];
        }

        return $conditional_post_parameters;
    }

    /**
     * Delete the assigned category
     *
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route\Validate::itemCategory(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        $item_category = (new ItemCategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            \App\Response\Responses::notFound(trans('entities.item-category'));
        }

        try {
            (new ItemCategory())->find($item_category_id)->delete();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->items($resource_type_id, $resource_id),
                $cache_key->resourceTypeItems($resource_type_id)
            ]);

            if (in_array((int) $resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->items($resource_type_id, $resource_id),
                    $cache_key->resourceTypeItems($resource_type_id)
                ]);
            }

            \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            \App\Response\Responses::notFound(trans('entities.item-category'), $e);
        }
    }
}
