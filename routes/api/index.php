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
        )->name('index.show');

        Route::options(
            '',
            [App\Http\Controllers\View\IndexController::class, 'optionsIndex']
        )->name('index.show.options');

        Route::get(
            'changelog',
            [App\Http\Controllers\View\IndexController::class, 'changeLog']
        )->name('index.changelog');

        Route::options(
            'changelog',
            [App\Http\Controllers\View\IndexController::class, 'optionsChangeLog']
        )->name('index.changelog.options');

        Route::get(
            'status',
            [App\Http\Controllers\View\IndexController::class, 'status']
        )->name('index.status');

        Route::options(
            'status',
            [App\Http\Controllers\View\IndexController::class, 'optionsStatus']
        )->name('index.status.options');

    }
);
