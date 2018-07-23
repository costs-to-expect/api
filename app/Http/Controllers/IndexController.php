<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Manage items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class IndexController extends Controller
{
    /**
     * Return all routes
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $routes_to_display = array();

        foreach (Route::getRoutes() as $route) {
            if (starts_with($route->uri, 'api-v1') === true) {
                if (isset($routes_to_display[$route->uri]['methods'])) {
                    $routes_to_display[$route->uri]['methods'] = array_merge($route->methods,
                        $routes_to_display[$route->uri]['methods']);
                } else {
                    $routes_to_display[$route->uri]['methods'] = $route->methods;
                }

                $routes_to_display[$route->uri]['uri'] = $route->uri;
            }
        }

        ksort($routes_to_display);

        return response()->json(
            $routes_to_display,
            200
        );
    }
}
