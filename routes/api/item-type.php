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
            'item-types',
            [\App\Http\Controllers\View\ItemTypeController::class, 'index']
        )->name('item-type.list');

        Route::options(
            'item-types',
            [\App\Http\Controllers\View\ItemTypeController::class, 'optionsIndex']
        );

        Route::get(
            'item-types/{item_type_id}',
            [\App\Http\Controllers\View\ItemTypeController::class, 'show']
        )->name('item-type.show');

        Route::options(
            'item-types/{item_type_id}',
            [\App\Http\Controllers\View\ItemTypeController::class, 'optionsShow']
        );

    }
);
