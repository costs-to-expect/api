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

        Route::get(
            '',
            [App\Http\Controllers\View\IndexController::class, 'index']
        );

        Route::options(
            '',
            [App\Http\Controllers\View\IndexController::class, 'optionsIndex']
        );

        Route::get(
            'changelog',
            [App\Http\Controllers\View\IndexController::class, 'changeLog']
        );

        Route::options(
            'changelog',
            [App\Http\Controllers\View\IndexController::class, 'optionsChangeLog']
        );

        Route::get(
            'status',
            [App\Http\Controllers\View\IndexController::class, 'status']
        );

        Route::options(
            'status',
            [App\Http\Controllers\View\IndexController::class, 'optionsStatus']
        );

    }
);
