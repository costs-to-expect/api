<?php

namespace App\Http\Route\Validators;

use App\Models\Resource as ResourceModel;

/**
 * Validate the route params to a resource
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Resource
{
    /**
     * Validate the route params are valid
     *
     * @param string|int $category_id
     *
     * @return boolean
     */
    static public function validate($resource_type_id, $resource_id)
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
}
