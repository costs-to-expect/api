<?php

namespace App\Http\Controllers;

use App\Models\RequestErrorLog;
use App\Validators\RequestErrorLog as RequestErrorLogValidator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Log a request error, these are logged when the web app receives an unexpected
     * http status code response
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function createApiRequestError(Request $request): JsonResponse
    {
        $validator = (new RequestErrorLogValidator())->create($request);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator);
        }

        try {
            $request_log = new RequestErrorLog([
                'method' => $request->input('method'),
                'expected_status_code' => $request->input('expected_status_code'),
                'returned_status_code' => $request->input('returned_status_code'),
                'request_uri' => $request->input('request_uri'),
            ]);
            $request_log->save();
        } catch (Exception $e) {
            echo $e->getMessage();

            return response()->json(
                [
                    'message' => 'Error creating request log error'
                ],
                500
            );
        }

        return response()->json(
            [
                'message' => 'API request error log entry created'
            ],
            201
        );
    }

    /**
     * Generate the OPTIONS request for the request error list
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function optionsRequests(Request $request): JsonResponse
    {
        return $this->generateOptionsForIndex(
            'api.descriptions.request_error.GET_index',
            'api.routes.request_error.parameters.collection',
            'api.descriptions.request_error.POST',
            'api.routes.request_error.fields'
        );
    }
}
