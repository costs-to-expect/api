<?php

namespace App\Http\Controllers;

use App\Response\Cache;
use App\Request\Route;
use App\Models\ItemCategory;
use App\Models\Transformers\ItemCategory as ItemCategoryTransformer;
use App\Request\Validate\ItemCategory as ItemCategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

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

        $cache_control = new Cache\Control($this->user_id);
        $cache_key = new Cache\Key();

        $validator = (new ItemCategoryValidator)->create();
        \App\Request\BodyValidation::validateAndReturnErrors(
            $validator,
            (new \App\Option\Value\Category())->allowedValues($resource_type_id)
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

            $cache_trash = new Cache\Trash(
                $cache_control,
                [
                    $cache_key->items($resource_type_id, $resource_id),
                    $cache_key->resourceTypeItems($resource_type_id)
                ],
                $resource_type_id,
                $this->public_resource_types,
                $this->permittedUsers($resource_type_id)
            );
            $cache_trash->all();

        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemCategoryTransformer((new ItemCategory())->instanceToArray($item_category)))->asArray(),
            201
        );
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

        $cache_control = new Cache\Control($this->user_id);
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
            $item_category->delete();

            $cache_trash = new Cache\Trash(
                $cache_control,
                [
                    $cache_key->items($resource_type_id, $resource_id),
                    $cache_key->resourceTypeItems($resource_type_id)
                ],
                $resource_type_id,
                $this->public_resource_types,
                $this->permittedUsers($resource_type_id)
            );
            $cache_trash->all();

            \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            \App\Response\Responses::notFound(trans('entities.item-category'));
        }
    }
}
