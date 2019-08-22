<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\ItemSubCategory as ItemSubCategoryModel;

/**
 * Validate the route params to an item category
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubCategory
{
    /**
     * Validate the route params are valid
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     * @param string|int $item_id
     * @param string|int $item_category_id
     * @param string|int $item_sub_category_id
     *
     * @return boolean
     */
    static public function validate(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        $item_sub_category_id
    ): bool {
        if (
            $resource_type_id === 'nill' ||
            $resource_id === 'nill' ||
            $item_id === 'nill' ||
            $item_category_id === 'nill' ||
            $item_sub_category_id === 'nill' ||
            (new ItemSubCategoryModel())->where('item_category_id', '=', $item_category_id)
                ->whereHas('item_category', function ($query) use ($item_id, $resource_id, $resource_type_id) {
                    $query->where('item_id', '=', $item_id)
                        ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                            $query->where('resource_id', '=', $resource_id)
                                ->whereHas('resource', function ($query) use ($resource_type_id) {
                                    $query->where('resource_type_id', '=', $resource_type_id);
                                });
                        });
                })
                ->find($item_sub_category_id)
                ->exists() === false
        ) {
            return false;
        }

        return true;
    }
}
