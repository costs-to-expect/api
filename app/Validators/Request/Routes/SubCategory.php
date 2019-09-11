<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\SubCategory as SubCategoryModel;

/**
 * Validate the route params to a sub category
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubCategory
{
    /**
     * Validate that the user is able to view the requested subcategory based
     * on their permitted resource types, needs to be in their group or public
     *
     * @param string|int $category_id
     * @param string|int $subcategory_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    static public function existsToUserForViewing(
        $category_id,
        $subcategory_id,
        array $permitted_resource_types
    ): bool
    {
        if (
            $category_id === 'nill' ||
            $subcategory_id === 'nill' ||
            (new SubCategoryModel())->existsToUser(
                $category_id,
                $subcategory_id,
                $permitted_resource_types
            ) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validate that the user is able to manage the requested subcategor
     * based on their permitted resource types, needs to be in their group
     *
     * @param string|int $category_id
     * @param string|int $subcategory_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    static public function existsToUserForManagement(
        $category_id,
        $subcategory_id,
        array $permitted_resource_types
    ): bool
    {
        if (
            $category_id === 'nill' ||
            $subcategory_id === 'nill' ||
            (new SubCategoryModel())->existsToUser(
                $category_id,
                $subcategory_id,
                $permitted_resource_types,
                true
            ) === false
        ) {
            return false;
        }

        return true;
    }
}
