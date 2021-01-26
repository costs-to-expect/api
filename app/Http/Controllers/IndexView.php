<?php

namespace App\Http\Controllers;

use App\Option\Changelog;
use App\Option\Root;
use App\Response\Header\Header;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use SplFileObject;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class IndexView extends Controller
{
    /**
     * Return all routes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
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
                    'readme' => $config['readme'],
                    'documentation' => $config['documentation']
                ],
                'routes' => $routes_to_display
            ],
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request
     */
    public function optionsIndex()
    {
        $response = new Changelog(['view' => ($this->user_id !== null)]);

        return $response->create()->response();
    }

    /**
     * Generate the change log request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeLog()
    {
        $changes = [];

        $changelog = new SplFileObject(public_path() . '/../CHANGELOG.md');
        $i = 0;
        $section = null;

        while (!$changelog->eof()) {

            $line = trim($changelog->fgets());

            if ($line !== '') {

                if (strpos($line, '## [v') !== false) {

                    ++$i;
                    $changes[$i]['release'] = trim(str_replace('##', '', $line));
                    $section = null;
                }

                if (strpos($line, '###') !== false) {
                    $section = strtolower(trim(str_replace('###', '', $line)));
                }

                if ($section !== null && strpos($line, '-') !== false) {
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
     */
    public function optionsChangeLog()
    {
        $response = new Root(['view' => ($this->user_id !== null)]);

        return $response->create()->response();
    }
}
