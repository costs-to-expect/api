<?php
declare(strict_types=1);

namespace App\Request\Route\Validator;

use App\Models\ResourceTypeAccess;

/**
 * Validate the route params to a resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType
{
    /**
     * Validate that the user is able to view the requested resource type based
     * on their permitted resource types, needs to be in their group or public
     *
     * @param string|int $resource_type_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    public static function existsToUserForViewing(
        $resource_type_id,
        array $permitted_resource_types
    ): bool
    {
        return !(
            (new ResourceTypeAccess())->resourceTypeExistsToUser(
                (int) $resource_type_id,
                $permitted_resource_types
            ) === false
        );
    }

    /**
     * Validate that the user is able to manage the requested resource type
     * based on their permitted resource types, needs to be in their group
     *
     * @param string|int $resource_type_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    public static function existsToUserForManagement(
        $resource_type_id,
        array $permitted_resource_types
    ): bool
    {
        return !(
            (new ResourceTypeAccess())->resourceTypeExistsToUser(
                (int) $resource_type_id,
                $permitted_resource_types,
                true
            ) === false
        );
    }
}
