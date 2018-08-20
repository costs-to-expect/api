<?php

namespace App\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

/**
 * Convert hashed GET params into their ids
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ConvertHashIds
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $min_length = Config::get('api.hashids.min_length');

        $params = [
            'category_id' => new Hashids(Config::get('api.hashids.category'), $min_length),
            'sub_category_id' => new Hashids(Config::get('api.hashids.sub_category'), $min_length),
            'resource_type_id' => new Hashids(Config::get('api.hashids.resource_type'), $min_length),
            'resource_id' => new Hashids(Config::get('api.hashids.resource'), $min_length),
            'item_id' => new Hashids(Config::get('api.hashids.item'), $min_length),
            'item_category_id' => new Hashids(Config::get('api.hashids.item_category'), $min_length),
            'item_sub_category_id' => new Hashids(Config::get('api.hashids.item_sub_category'), $min_length),
        ];

        foreach ($params as $param => $hasher) {
            if ($request->route($param) !== null) {
                $id = $hasher->decode($request->route($param));
                if (is_array($id) && array_key_exists(0, $id)) {
                    $request->route()->setParameter($param, $id[0]);
                } else {
                    $request->route()->setParameter($param, 'nill');
                }
            }
        }

        return $next($request);
    }
}
