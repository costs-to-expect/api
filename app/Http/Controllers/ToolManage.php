<?php

namespace App\Http\Controllers;

use App\HttpResponse\Responses;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ToolManage extends Controller
{
    public function cache(): JsonResponse
    {
        $cache_control = new \App\Cache\Control(true, $this->user_id);

        $keys = $cache_control->fetchMatchingCacheKeys('', true);

        return response()->json(
            [
                'cached_keys' => count($keys),
            ],
            200,
        );
    }

    public function deleteCache(): JsonResponse
    {
        $cache_control = new \App\Cache\Control(true, $this->user_id);

        $keys = $cache_control->fetchMatchingCacheKeys('', true);

        foreach ($keys as $key) {
            $cache_control->clearCacheKeyByItsFullName($key['key']);
        }

        // This will leave two cache keys set in the base controller
        return Responses::successNoContent();
    }
}
