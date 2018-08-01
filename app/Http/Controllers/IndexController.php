<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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
            if (starts_with($route->uri, Config::get('version.prefix') ) === true) {
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
            [
                'api' => [
                    'version' => Config::get('version.version'),
                    'prefix' => Config::get('version.prefix'),
                    'release_date' => Config::get('version.release_date'),
                    'changelog' => Config::get('version.changelog')
                ],
                'routes' => $routes_to_display
            ],
            200
        );
    }

    /**
     * Generate the OPTIONS request
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsIndex(Request $request)
    {
        $routes = [
            'GET' => [
                'description' => Config::get('descriptions.api.GET_index'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }
}
