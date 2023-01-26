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
            'queue',
            [App\Http\Controllers\View\QueueController::class, 'index']
        );

        Route::options(
            'queue',
            [App\Http\Controllers\View\QueueController::class, 'optionsIndex']
        );

        Route::get(
            'queue/{queue_id}',
            [App\Http\Controllers\View\QueueController::class, 'show']
        );

        Route::options(
            'queue/{queue_id}',
            [App\Http\Controllers\View\QueueController::class, 'optionsShow']
        );

    }
);
