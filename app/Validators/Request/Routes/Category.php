<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\ResourceTypeAccess;

/**
 * Validate the route params to a category
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category
{
    /**
     * Validate that the user is able to view the requested resource type based
     * on their permitted resource types, needs to be in their group or public
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    static public function existsToUserForViewing(
        $resource_type_id,
        $category_id,
        array $permitted_resource_types
    ): bool
    {
        if (
            $category_id === 'nill' ||
            (new ResourceTypeAccess())->categoryExistsToUser(
                $resource_type_id,
                $category_id,
                $permitted_resource_types
            ) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validate that the user is able to manage the requested resource type
     * based on their permitted resource types, needs to be in their group
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    static public function existsToUserForManagement(
        $resource_type_id,
        $category_id,
        array $permitted_resource_types
    ): bool
    {
        if (
            $category_id === 'nill' ||
            (new ResourceTypeAccess())->categoryExistsToUser(
                $resource_type_id,
                $category_id,
                $permitted_resource_types,
                true
            ) === false
        ) {
            return false;
        }

        return true;
    }
}
