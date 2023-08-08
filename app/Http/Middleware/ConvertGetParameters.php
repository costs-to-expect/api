<?php

namespace App\Http\Middleware;

use App\HttpRequest\Hash;
use Closure;
use Illuminate\Http\Request;

/**
 * Convert hashed GET params, decode the value and reset in the request
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
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
        $hash = new Hash();

        $params_to_convert = [
            'category',
            'subcategory',
            'resource-type',
            'item',
            'item-subtype',
            'item-type',
        ];
        

        foreach ($params_to_convert as $param) {
            $param_value = $request->query($param);
            if ($param_value !== null) {
                $id = $hash->decode($param, $param_value);
                ($id !== false && is_int($id)) ? $value = $id : $value = null;
                $request->merge([$param => $value]);
            }
        }

        return $next($request);
    }
}
