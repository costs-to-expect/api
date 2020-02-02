<?php

namespace App\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Convert hashed GET params, decode the value and reset in the request
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ConvertGetParameters
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $config = Config::get('api.app.hashids');

        $min_length = $config['min_length'];

        $parameters = [
            'category' => new Hashids($config['category'], $min_length),
            'subcategory' => new Hashids($config['subcategory'], $min_length),
            'resource-type' => new Hashids($config['resource_type'], $min_length),
        ];

        $params_to_convert = array_keys($parameters);

        foreach ($params_to_convert as $param) {
            $param_value = $request->query($param);
            if ($param_value !== null) {
                $id = $parameters[$param]->decode($param_value);
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
