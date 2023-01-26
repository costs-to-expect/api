<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\RequestError;
use App\Models\RequestErrorLog;
use App\HttpRequest\Validate\RequestErrorLog as RequestErrorLogValidator;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestController extends Controller
{
    /**
    * Log a request error, these are logged when the web app receives an unexpected
    * http status code response
    *
    * @return JsonResponse
    */
    public function createErrorLog(Request $request): JsonResponse
    {
        $validator = (new RequestErrorLogValidator())->create();

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        try {
            $request_error_log = new RequestErrorLog([
                'method' => $request->input('method'),
                'source' => $request->input('source'),
                'expected_status_code' => $request->input('expected_status_code'),
                'returned_status_code' => $request->input('returned_status_code'),
                'request_uri' => $request->input('request_uri'),
                'debug' => $request->input('debug')
            ]);
            $request_error_log->save();

            if ($request->input('returned_status_code') !== '404') {
                event(new RequestError([
                    'method' => $request->input('method'),
                    'source' => $request->input('source'),
                    'expected_status_code' => $request->input('expected_status_code'),
                    'returned_status_code' => $request->input('returned_status_code'),
                    'request_uri' => $request->input('request_uri'),
                    'referer' => $request->server('HTTP_REFERER', 'NOT SET!'),
                    'debug' => $request->input('debug')
                ]));
            }
        } catch (Exception $e) {
            return \App\HttpResponse\Response::failedToSaveModelForCreate($e);
        }

        return \App\HttpResponse\Response::successNoContent();
    }
}
