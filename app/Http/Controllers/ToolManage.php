<?php

namespace App\Http\Controllers;

use App\Response\Cache;
use App\Response\Responses;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ToolManage extends Controller
{
     /**
     * View the number of cached keys
     *
     * @return JsonResponse
     */
    public function cache(): JsonResponse
    {
        $cache_control = new Cache\Control(true, $this->user_id);

        $keys = $cache_control->fetchMatchingCacheKeys('', true);

        return response()->json(
            [
                'cached_keys' => count($keys),
            ],
            200,
        );
    }

    /**
     * Delete the cache
     *
     * @return JsonResponse
     */
    public function deleteCache(): JsonResponse
    {
        $cache_control = new Cache\Control(true, $this->user_id);

        $keys = $cache_control->fetchMatchingCacheKeys('', true);

        foreach ($keys as $key) {
            $cache_control->clearCacheKeyByItsFullName($key['key']);
        }

        return Responses::successNoContent();
    }
}
