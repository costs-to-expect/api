<?php

namespace App\Http\Controllers;

use App\Utilities\Header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use SplFileObject;

/**
 * Manage items
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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

        $config = Config::get('api.app.version');

        foreach (Route::getRoutes() as $route) {
            if (Str::startsWith($route->uri, $config['prefix']) === true) {
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

        $headers = new Header();

        return response()->json(
            [
                'api' => [
                    'version' => $config['version'],
                    'prefix' => $config['prefix'],
                    'release_date' => $config['release_date'],
                    'changelog' => $config['changelog'],
                    'readme' => $config['readme']
                ],
                'routes' => $routes_to_display
            ],
            200,
            $headers->headers()
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
                    'description' => trans('route-descriptions.api_GET_index'),
                    'authentication' => [
                        'required' => false,
                        'authenticated' => ($this->user_id !== null) ? true : false
                    ],
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
        $section = null;

        while (!$changelog->eof()) {

            $line = trim($changelog->fgets());

            if (strlen($line) > 0) {

                if (strpos($line, '## [v') !== false) {

                    ++$i;
                    $changes[$i]['release'] = trim(str_replace('##', '', $line));
                    $section = null;
                }

                if (strpos($line, '###') !== false) {
                    $section = strtolower(trim(str_replace('###', '', $line)));
                }

                if (strpos($line, '-') !== false && $section !== null) {
                    $changes[$i][$section][] = trim(str_replace('- ', '', $line));
                }
            }
        }

        $headers = new Header();

        return response()->json(
            [
                'releases' => array_values($changes)
            ],
            200,
            array_merge(
                [
                    'X-Total-Count' => ($i + 1)
                ],
                $headers->headers()
            )
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
                    'description' => trans('route-descriptions.api_GET_changelog'),
                    'authentication' => [
                        'required' => false,
                        'authenticated' => ($this->user_id !== null) ? true : false
                    ],
                    'parameters' => []
                ]
            ]
        );
    }
}
