<?php

namespace App\Http\Middleware;

use App\HttpResponse\Response;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Restrict a route to callers who present the shared internal API key,
 * used to gate endpoints only trusted first-party backends should call
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class VerifyInternalApiKey
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
        $expected = Config::get('api.app.config.internal_api_key');
        $provided = $request->header('X-Internal-Api-Key');

        if (
            empty($expected) ||
            is_string($provided) === false ||
            hash_equals($expected, $provided) === false
        ) {
            return Response::invalidInternalApiKey();
        }

        return $next($request);
    }
}
