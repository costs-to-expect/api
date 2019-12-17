<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Exception;
use Illuminate\Http\Request;

/**
 * Log requests to the API
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param Request $request
     * @param $response
     *
     * @return void
     */
    public function terminate($request, $response)
    {
        try {
            $log = new RequestLog([
                'method' => $request->method(),
                'request' => $request->fullUrl(),
                'source' => $request->header('X-Source', 'api')
            ]);
            $log->save();
        } catch (Exception $e) {
            // We don't care for now
        }
    }
}
