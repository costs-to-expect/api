<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
        'middleware' => [
            'convert.route.parameters',
            'convert.get.parameters'
        ]
    ],
    static function () {

        Route::options(
            'request/error-log',
            [ App\Http\Controllers\View\RequestController::class, 'optionsErrorLog']
        );

        Route::get(
            'request/error-log',
            [ App\Http\Controllers\View\RequestController::class, 'errorLog']
        );

        Route::post(
            'request/error-log',
            [ App\Http\Controllers\Action\RequestController::class, 'createErrorLog']
        );

    }
);
