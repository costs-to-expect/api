<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ErrorController extends BaseController
{
    /**
     * Log a request error in contacting the API, web app posts the error when
     * the Request helper encounters and error
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function createApiConnectionError(Request $request): JsonResponse
    {
        return response()->json(
            [],
            201
        );
    }
}
