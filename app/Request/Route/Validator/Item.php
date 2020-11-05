<?php
declare(strict_types=1);

namespace App\Request\Route\Validator;

use App\Models\ResourceTypeAccess;

/**
 * Validate the route params to an item
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item
{
    /**
     * Validate that the user is able to view the requested item based
     * on their permitted resource types, needs to be in their group or public
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     * @param string|int $item_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    public static function existsToUserForViewing(
        $resource_type_id,
        $resource_id,
        $item_id,
        array $permitted_resource_types
    ): bool
    {
        return !(
            (new ResourceTypeAccess())->itemExistsToUser(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                $permitted_resource_types
            ) === false
        );
    }

    /**
     * Validate that the user is able to manage the requested item
     * based on their permitted resource types, needs to be in their group
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     * @param string|int $item_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    public static function existsToUserForManagement(
        $resource_type_id,
        $resource_id,
        $item_id,
        array $permitted_resource_types
    ): bool
    {
        return !(
            (new ResourceTypeAccess())->itemExistsToUser(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                $permitted_resource_types,
                true
            ) === false
        );
    }
}
