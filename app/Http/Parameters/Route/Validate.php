<?php

namespace App\Http\Parameters\Route;

use App\Http\Parameters\Route\Validators\Resource;
use App\Http\Parameters\Route\Validators\ResourceType;

/**
 * Validate the set route parameters, redirect to 404 if invalid
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Validate
{
    static public function resourceType($resource_type_id)
    {
        if (ResourceType::validate($resource_type_id) === false) {
            self::return404('Resource type not found');
        }
    }

    static public function resource($resource_type_id, $resource_id)
    {
        if (Resource::validate($resource_type_id, $resource_id) === false) {
            self::return404('Resource not found');
        }
    }

    static protected function return404($message)
    {
        response()->json(
            [
                'message' => $message
            ],
            404
        )->send();
        die;
    }
}
