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
            'currencies',
            [App\Http\Controllers\View\CurrencyController::class, 'index']
        );

        Route::options(
            'currencies',
            [App\Http\Controllers\View\CurrencyController::class, 'optionsIndex']
        );

        Route::get(
            'currencies/{currency_id}',
            [App\Http\Controllers\View\CurrencyController::class, 'show']
        )->name('currency.show');

        Route::options(
            'currencies/{currency_id}',
            [App\Http\Controllers\View\CurrencyController::class, 'optionsShow']
        );

    }
);
