<?php

namespace App\Http\Controllers;

use App\Events\RequestError;
use App\Models\RequestErrorLog;
use App\Request\Validate\RequestErrorLog as RequestErrorLogValidator;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Manage categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestManage extends Controller
{
     /**
     * Log a request error, these are logged when the web app receives an unexpected
     * http status code response
     *
     * @return JsonResponse
     */
    public function createErrorLog(): JsonResponse
    {
        $validator = (new RequestErrorLogValidator())->create();
        \App\Request\BodyValidation::validateAndReturnErrors($validator);

        try {
            $request_error_log = new RequestErrorLog([
                'method' => request()->input('method'),
                'source' => request()->input('source'),
                'expected_status_code' => request()->input('expected_status_code'),
                'returned_status_code' => request()->input('returned_status_code'),
                'request_uri' => request()->input('request_uri'),
                'debug' => request()->input('debug')
            ]);
            $request_error_log->save();

            if (request()->input('returned_status_code') !== '404') {
                event(new RequestError([
                    'method' => request()->input('method'),
                    'source' => request()->input('source'),
                    'expected_status_code' => request()->input('expected_status_code'),
                    'returned_status_code' => request()->input('returned_status_code'),
                    'request_uri' => request()->input('request_uri'),
                    'referer' => request()->server('HTTP_REFERER', 'NOT SET!'),
                    'debug' => request()->input('debug')
                ]));
            }

        } catch (Exception $e) {
            return \App\Response\Responses::failedToSaveModelForCreate();
        }

        return \App\Response\Responses::successNoContent();
    }
}
