<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ToolManage extends Controller
{
     /**
     * Clear the cache for the authenticated user
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        return \App\Response\Responses::successNoContent();
    }
}
