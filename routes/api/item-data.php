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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [App\Http\Controllers\View\ItemDataController::class, 'index']
        )->name('item-data.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [App\Http\Controllers\View\ItemDataController::class, 'optionsIndex']
        )->name('item-data.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [App\Http\Controllers\View\ItemDataController::class, 'show']
        )->name('item-data.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [App\Http\Controllers\View\ItemDataController::class, 'optionsShow']
        )->name('item-data.show.options');

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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [App\Http\Controllers\Manage\ItemDataController::class, 'create']
        )->name('item-data.create');

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [App\Http\Controllers\Manage\ItemDataController::class, 'delete']
        )->name('item-data.delete');

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [App\Http\Controllers\Manage\ItemDataController::class, 'update']
        )->name('item-data.update');

    }
);
