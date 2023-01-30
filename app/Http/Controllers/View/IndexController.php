<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\HttpOptionResponse\Status;
use App\HttpResponse\Header;
use App\HttpOptionResponse\Changelog;
use App\HttpOptionResponse\Root;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use SplFileObject;

use function App\Http\Controllers\str_contains;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class IndexController extends Controller
{
    public function index()
    {
        $routes_to_display = [];

        $config = Config::get('api.app.version');

        foreach (Route::getRoutes() as $route) {
            if (Str::startsWith($route->uri, $config['prefix']) === true) {
                if (isset($routes_to_display[$route->uri]['methods'])) {
                    $routes_to_display[$route->uri]['methods'] = [
                        ...$route->methods,
                        ...$routes_to_display[$route->uri]['methods']
                    ];
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

    public function optionsIndex()
    {
        $response = new Changelog(['view' => ($this->user_id !== null)]);

        return $response->create()->response();
    }

    public function changeLog()
    {
        $changes = [];

        $changelog = new SplFileObject(public_path() . '/../CHANGELOG.md');
        $i = 0;
        $section = null;

        while (!$changelog->eof()) {
            $line = trim($changelog->fgets());

            if ($line !== '') {
                if (Str::contains($line, '## [v')) {
                    ++$i;
                    $changes[$i]['release'] = trim(str_replace('##', '', $line));
                    $section = null;
                }

                if (Str::contains($line, '###')) {
                    $section = strtolower(trim(str_replace('###', '', $line)));
                }

                if ($section !== null && Str::contains($line, '-')) {
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
            [
                ...[
                    'X-Total-Count' => ($i + 1)
                ],
                ...$headers->headers()
            ]
        );
    }

    public function optionsChangeLog()
    {
        $response = new Root(['view' => ($this->user_id !== null)]);

        return $response->create()->response();
    }

    public function status()
    {
        $cache_config = Config::get('api.app.cache');

        return response()->json(
            [
                'environment' =>  App::environment(),
                'cache' => $cache_config['enable']
            ]
        );
    }

    public function optionsStatus()
    {
        $response = new Status(['view' => ($this->user_id !== null)]);

        return $response->create()->response();
    }
}
