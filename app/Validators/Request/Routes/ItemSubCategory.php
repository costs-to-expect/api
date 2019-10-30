<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\PermittedUser;

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
     * Validate that the user is able to view the requested item subcategory
     * based on their permitted resource types, needs to be in their group or
     * public
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     * @param string|int $item_id
     * @param string|int $item_category_id
     * @param string|int $item_subcategory_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    static public function existsToUserForViewing(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        $item_subcategory_id,
        array $permitted_resource_types
    ): bool
    {
        if (
            $resource_type_id === 'nill' ||
            $resource_id === 'nill' ||
            $item_id === 'nill' ||
            $item_category_id === 'nill' ||
            $item_subcategory_id === 'nill' ||
            (new PermittedUser())->itemSubcategoryExistsToUser(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id,
                $item_subcategory_id,
                $permitted_resource_types
            ) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validate that the user is able to manage the requested item subcategory
     * based on their permitted resource types, needs to be in their group
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     * @param string|int $item_id
     * @param string|int $item_category_id
     * @param string|int $item_subcategory_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    static public function existsToUserForManagement(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        $item_subcategory_id,
        array $permitted_resource_types
    ): bool
    {
        if (
            $resource_type_id === 'nill' ||
            $resource_id === 'nill' ||
            $item_id === 'nill' ||
            $item_category_id === 'nill' ||
            $item_subcategory_id === 'nill' ||
            (new PermittedUser())->itemSubcategoryExistsToUser(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id,
                $item_subcategory_id,
                $permitted_resource_types,
                true
            ) === false
        ) {
            return false;
        }

        return true;
    }
}
