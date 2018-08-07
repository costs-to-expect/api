<?php

namespace App\Http\Route\Validators;

use App\Models\Item as ItemModel;

/**
 * Validate the route params to an item
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item
{
    /**
     * Validate the route params are valid
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     * @param string|int $item_id
     *
     * @return boolean
     */
    static public function validate($resource_type_id, $resource_id, $item_id)
    {
        if (
            $resource_type_id === 'nill' ||
            $resource_id === 'nill' ||
            $item_id === 'nill' ||
            (new ItemModel())->where('resource_id', '=', $resource_id)
                ->whereHas('resource', function ($query) use ($resource_type_id) {
                    $query->where('resource_type_id', '=', $resource_type_id);
                })
                ->find($item_id)->exists() === false
        ) {
            return false;
        }

        return true;
    }
}
