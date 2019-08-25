<?php
declare(strict_types=1);

namespace App\Validators\Request\Routes;

use App\Models\ResourceType as ResourceTypeModel;

/**
 * Validate the route params to a resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType
{
    /**
     * Validate the route params are valid
     *
     * @param string|int $resource_type_id
     *
     * @return boolean
     */
    static public function validate($resource_type_id): bool
    {
        if (
            $resource_type_id === 'nill' ||
            (new ResourceTypeModel)->find($resource_type_id)->exists() === false
        ) {
            return false;
        }

        return true;
    }
}
