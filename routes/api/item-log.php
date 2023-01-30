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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log',
            [App\Http\Controllers\View\ItemLogController::class, 'index']
        )->name('item-log.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log',
            [App\Http\Controllers\View\ItemLogController::class, 'optionsIndex']
        )->name('item-log.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log/{item_log_id}',
            [App\Http\Controllers\View\ItemLogController::class, 'show']
        )->name('item-log.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log/{item_log_id}',
            [App\Http\Controllers\View\ItemLogController::class, 'optionsShow']
        )->name('item-log.show.options');

    }
);

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
        'middleware' => [
            'auth:sanctum',
            'convert.route.parameters'
        ]
    ],
    static function () {

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log',
            [App\Http\Controllers\Manage\ItemLogController::class, 'create']
        )->name('item-log.create');

    }
);
