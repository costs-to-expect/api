<?php

namespace App\Http\Controllers;

use App\Response\Cache;
use App\Request\Route;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Models\Transformers\ItemSubcategory as ItemSubcategoryTransformer;
use App\Request\Validate\ItemSubcategory as ItemSubcategoryValidator;
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
class ItemSubcategoryManage extends Controller
{
    /**
     * Assign the sub category
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function create(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
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

        if ($item_category_id === 'nill') {
            \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_category_id);

        $validator = (new ItemSubcategoryValidator)->create(['category_id' => $item_category->category_id]);
        \App\Request\BodyValidation::validateAndReturnErrors(
            $validator,
            (new \App\Option\Value\Subcategory())->allowedValues($item_category->category_id)
        );

        try {
            $subcategory_id = $this->hash->decode('subcategory', request()->input('subcategory_id'));

            if ($subcategory_id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $item_sub_category = new ItemSubcategory([
                'item_category_id' => $item_category_id,
                'sub_category_id' => $subcategory_id
            ]);
            $item_sub_category->save();

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
            (new ItemSubcategoryTransformer((new ItemSubcategory())->instanceToArray($item_sub_category)))->asArray(),
            201
        );
    }

    /**
     * Delete the assigned sub category
     *
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id,
     * @param string $item_subcategory_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_subcategory_id
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

        if ($item_category_id === 'nill' || $item_subcategory_id === 'nill') {
            \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        try {
            $item_sub_category->delete();

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
            \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }
    }
}
