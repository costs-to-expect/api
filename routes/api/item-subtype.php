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
            'item-types/{item_type_id}/item-subtypes',
            [App\Http\Controllers\View\ItemSubtypeController::class, 'index']
        );

        Route::options(
            'item-types/{item_type_id}/item-subtypes',
            [App\Http\Controllers\View\ItemSubtypeController::class, 'optionsIndex']
        );

        Route::get(
            'item-types/{item_type_id}/item-subtypes/{item_subtype_id}',
            [App\Http\Controllers\View\ItemSubtypeController::class, 'show']
        )->name('item-subtype.show');

        Route::options(
            'item-types/{item_type_id}/item-subtypes/{item_subtype_id}',
            [App\Http\Controllers\View\ItemSubtypeController::class, 'optionsShow']
        );

    }
);
