<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\Resource as ResourceModel;

/**
 * Validate the route params to a resource
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource
{
    /**
     * Validate the route params are valid
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     *
     * @return boolean
     */
    static public function validate($resource_type_id, $resource_id): bool
    {
        if (
            $resource_type_id === 'nill' ||
            $resource_id === 'nill' ||
            (new ResourceModel())->where('resource_type_id', '=', $resource_type_id)
                ->find($resource_id)->exists() === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validate that the user is able to view the requested resource based
     * on their permitted resource types, needs to be in their group or public
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    static public function existsToUserForViewing(
        $resource_type_id,
        $resource_id,
        array $permitted_resource_types
    ): bool
    {
        if (
            $resource_type_id === 'nill' ||
            (new ResourceModel())->existsToUser(
                $resource_id,
                $resource_type_id,
                $permitted_resource_types,
                'view'
            ) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validate that the user is able to manage the requested resource
     * based on their permitted resource types, needs to be in their group
     *
     * @param string|int $resource_type_id
     * @param string|int $resource_id
     * @param array $permitted_resource_types
     *
     * @return boolean
     */
    static public function existsToUserForManagement(
        $resource_type_id,
        $resource_id,
        array $permitted_resource_types
    ): bool
    {
        if (
            $resource_type_id === 'nill' ||
            (new ResourceModel())->existsToUser(
                $resource_id,
                $resource_type_id,
                $permitted_resource_types,
                'manage'
            ) === false
        ) {
            return false;
        }

        return true;
    }
}
