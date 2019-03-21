<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use SplFileObject;

/**
 * Manage items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
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
            if (starts_with($route->uri, Config::get('api.version.prefix') ) === true) {
                if (isset($routes_to_display[$route->uri]['methods'])) {
                    $routes_to_display[$route->uri]['methods'] = array_merge(
                        $route->methods,
                        $routes_to_display[$route->uri]['methods']
                    );
                } else {
                    $routes_to_display[$route->uri]['methods'] = $route->methods;
                }

                $routes_to_display[$route->uri]['uri'] = '/' . $route->uri;
            }
        }

        ksort($routes_to_display);

        return response()->json(
            [
                'api' => [
                    'version' => Config::get('api.version.version'),
                    'prefix' => Config::get('api.version.prefix'),
                    'release_date' => Config::get('api.version.release_date'),
                    'changelog' => Config::get('api.version.changelog'),
                    'readme' => Config::get('api.version.readme')
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
     */
    public function optionsIndex(Request $request)
    {
        $this->optionsResponse(
            [
                'GET' => [
                    'description' => config::get('api.route-descriptions.api.GET_index'),
                    'authenticated' => false,
                    'parameters' => []
                ]
            ]
        );
    }

    /**
     * Generate the change log request
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeLog(Request $request)
    {
        $changes = [];

        $changelog = new SplFileObject(public_path() . '/../CHANGELOG.md');
        $i = 0;

        while (!$changelog->eof()) {

            $line = trim($changelog->fgets());

            if (strlen($line) > 0) {

                if (strpos($line, '#') !== false) {

                    ++$i;
                    $changes[$i]['title'] = trim(str_replace('#', '', $line));
                }

                if (strpos($line, '*') !== false) {
                    $changes[$i]['changes'][] = trim(str_replace('* ', '', $line));
                }
            }
        }

        return response()->json(
            [
                'changes' => array_values($changes)
            ],
            200,
            [
                'X-Total-Count' => ($i + 1)
            ]
        );
    }

    /**
     * Generate the OPTIONS request for the change log
     *
     * @param Request $request
     */
    public function optionsChangeLog(Request $request)
    {
        $this->optionsResponse(
            [
                'GET' => [
                    'description' => config::get('api.route-descriptions.api.GET_changelog'),
                    'authenticated' => false,
                    'parameters' => []
                ]
            ]
        );
    }
}
