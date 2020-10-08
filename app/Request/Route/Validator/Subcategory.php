<?php
declare(strict_types=1);

namespace App\Request\Route\Validator;

use App\Models\ResourceTypeAccess;

/**
 * Validate the route params to a sub category
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Subcategory
{
    /**
     * Validate that the user is able to view the requested subcategory based
     * on their permitted resource types, needs to be in their group or public
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param integer $subcategory_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    public static function existsToUserForViewing(
        $resource_type_id,
        $category_id,
        $subcategory_id,
        array $permitted_resource_types
    ): bool
    {
        return !(
            $resource_type_id === 'nill' ||
            $category_id === 'nill' ||
            $subcategory_id === 'nill' ||
            (new ResourceTypeAccess())->subcategoryExistsToUser(
                (int) $resource_type_id,
                (int) $category_id,
                (int) $subcategory_id,
                $permitted_resource_types
            ) === false
        );
    }

    /**
     * Validate that the user is able to manage the requested subcategor
     * based on their permitted resource types, needs to be in their group
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param integer $subcategory_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    public static function existsToUserForManagement(
        $resource_type_id,
        $category_id,
        $subcategory_id,
        array $permitted_resource_types
    ): bool
    {
        return !(
            $resource_type_id === 'nill' ||
            $category_id === 'nill' ||
            $subcategory_id === 'nill' ||
            (new ResourceTypeAccess())->subcategoryExistsToUser(
                (int) $resource_type_id,
                (int) $category_id,
                (int) $subcategory_id,
                $permitted_resource_types,
                true
            ) === false
        );
    }
}
