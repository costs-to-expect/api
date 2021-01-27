<?php

namespace App\Http\Controllers;

use App\Option\Cache;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ToolView extends Controller
{
     /**
     * Options request for the tools/cache route
     *
     * @return JsonResponse
     */
    public function optionsCache(): JsonResponse
    {
        $response = new Cache(['view'=> $this->user_id !== null, 'manage'=> $this->user_id !== null]);

        return $response->create()->response();
    }
}
