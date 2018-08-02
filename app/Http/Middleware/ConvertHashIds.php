<?php

namespace App\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

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
        $hash_category = new Hashids(Config::get('api.hashids.category'), 10);
        $hash_sub_category = new Hashids(Config::get('api.hashids.sub_category'), 10);
        $resource_type = new Hashids(Config::get('api.hashids.resource_type'), 10);
        $resource = new Hashids(Config::get('api.hashids.resource'), 10);
        $item = new Hashids(Config::get('api.hashids.item'), 10);
        $item_category = new Hashids(Config::get('api.hashids.item_category'), 10);
        $item_sub_category = new Hashids(Config::get('api.hashids.item_sub_category'), 10);

        $params = [
            'category_id' => $hash_category,
            'sub_category_id' => $hash_sub_category,
            'resource_type_id' => $resource_type,
            'resource_id' => $resource,
            'item_id' => $item,
            'item_category_id' => $item_category,
            'item_sub_category_id' =>$item_sub_category,
        ];

        foreach ($params as $param => $hasher) {
            if ($request->route($param) !== null) {
                $id = $hasher->decode($request->route($param));
                if (is_array($id) && array_key_exists(0, $id)) {
                    $request->route()->setParameter($param, $id[0]);
                }
            }
        }

        return $next($request);
    }
}
