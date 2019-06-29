<?php

namespace App\Exceptions;

use App\Utilities\Response;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $status_code = 500;
        if (method_exists($exception, 'getStatusCode') === true) {
            $status_code = $exception->getStatusCode();
        }

        $message = $exception->getMessage();

        switch ($status_code) {
            case 404:
                Response::notFound();
                break;
            case 503:
                response()->json(
                    [
                        'message' => 'Down for maintenance, we should be back very soon'
                    ],
                    503
                )->send();
                exit;
                break;
            case 500:
                if (App::environment() === 'local') {
                    $response = [
                        'message' => $exception->getMessage(),
                        'trace' => $exception->getTraceAsString()
                    ];
                } else {
                    $response = [
                        'message' => 'Sorry, there has been an error, please try again later'
                    ];
                }

                response()->json(
                    $response,
                    500
                )->send();
                exit;
                break;
            default:
                $message = $exception->getMessage();
                break;
        }

        return response()->json(
            [
                'message' => $message,
                'trace' => $exception->getTraceAsString()
            ],
            $status_code
        );
    }
}
