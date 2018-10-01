<?php

namespace App\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

/**
 * Convert hashed GET params, decode the value and reset in the request
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ConvertGetParameters
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $min_length = Config::get('api.hashids.min_length');

        $parameters = [
            'category' => new Hashids(Config::get('api.hashids.category'), $min_length),
            'sub_category' => new Hashids(Config::get('api.hashids.sub_category'), $min_length)
        ];

        foreach ($parameters as $param => $hasher) {
            if ($request->query($param) !== null) {
                $id = $hasher->decode($request->query($param));
                if (is_array($id) && array_key_exists(0, $id)) {
                    $request->request->add([$param => $id[0]]);
                } else {
                    $request->request->add([$param => 'nill']);
                }
            }
        }

        return $next($request);
    }
}
