<?php

namespace App\Http\Middleware;

use App\HttpResponse\Response;
use App\HttpRequest\Hash;
use Closure;

/**
 * Convert hashed route params, decode the value and reset in the route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
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
        $hash = new Hash();

        $route_params = [
            'category_id' => 'category',
            'subcategory_id' => 'subcategory',
            'resource_type_id' => 'resource-type',
            'permitted_resource_type_id' => 'permitted-resource-type',
            'permitted_user_id' => 'permitted-user',
            'resource_id' => 'resource',
            'item_id' => 'item',
            'item_category_id' => 'item-category',
            'item_log_id' => 'item-log',
            'item_partial_transfer_id' => 'item-partial-transfer',
            'item_subcategory_id' => 'item-subcategory',
            'item_transfer_id' => 'item-transfer',
            'item_type_id' => 'item-type',
            'item_subtype_id' => 'item-subtype',
            'currency_id' => 'currency',
            'queue_id' => 'queue',
        ];

        $params_to_convert = array_keys($route_params);

        foreach ($params_to_convert as $param) {
            $param_value = $request->route($param);
            if ($param_value !== null) {
                $id = $hash->decode($route_params[$param], $param_value);

                if ($id === false) {
                    return Response::notFoundOrNotAccessible($route_params[$param]);
                }

                if (is_int($id) === false) {
                    return Response::notFoundOrNotAccessible($route_params[$param]);
                }

                $request->route()->setParameter($param, $id);
            }
        }

        return $next($request);
    }
}
