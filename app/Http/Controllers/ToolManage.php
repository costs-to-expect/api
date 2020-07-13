<?php

namespace App\Http\Controllers;

use App\Response\Cache;
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
        $cache_control = new Cache\Control($this->user_id);

        $keys = $cache_control->matchingPrivateCacheKeys('', true);

        foreach ($keys as $key) {
            $cache_control->clearCacheKeyByFullName($key['key']);
        }

        return response()->json(
            [
                'cleared_keys' => count($keys),
            ],
            200,
        );
    }
}
