<?php

namespace App\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

/**
 * Convert hashed route params, decode the value and reset in the route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ConvertRouteParameters
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
        $config = Config::get('api.app.hashids');

        $min_length = $config['min_length'];

        $route_params = [
            'category_id' => new Hashids($config['category'], $min_length),
            'subcategory_id' => new Hashids($config['subcategory'], $min_length),
            'resource_type_id' => new Hashids($config['resource_type'], $min_length),
            'resource_id' => new Hashids($config['resource'], $min_length),
            'item_id' => new Hashids($config['item'], $min_length),
            'item_category_id' => new Hashids($config['item_category'], $min_length),
            'item_partial_transfer_id' => new Hashids($config['item_partial_transfer'], $min_length),
            'item_subcategory_id' => new Hashids($config['item_subcategory'], $min_length),
            'item_type_id' => new Hashids($config['item_type'], $min_length),
        ];

        $params_to_convert = array_keys($route_params);

        foreach ($params_to_convert as $param) {
            $param_value = $request->route($param);
            if ($param_value !== null) {
                $id = $route_params[$param]->decode($param_value);
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
