<?php

namespace App\Http\Controllers;

use App\Option\ClearCache;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ToolView extends Controller
{
     /**
     * Clear the cache for the authenticated user
     *
     * @return JsonResponse
     */
    public function optionsClearCache(): JsonResponse
    {
        $response = new ClearCache(['manage'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
