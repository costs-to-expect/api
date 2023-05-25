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
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            [App\Http\Controllers\View\ItemController::class, 'index']
        )->name('item.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            [App\Http\Controllers\View\ItemController::class, 'optionsIndex']
        )->name('item.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [App\Http\Controllers\View\ItemController::class, 'show']
        )->name('item.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [App\Http\Controllers\View\ItemController::class, 'optionsShow']
        )->name('item.show.options');


        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            [App\Http\Controllers\Summary\View\ItemController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            [App\Http\Controllers\Summary\View\ItemController::class, 'optionsIndex']
        );

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
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            [App\Http\Controllers\Action\ItemController::class, 'create']
        )->name('item.create');

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [App\Http\Controllers\Action\ItemController::class, 'delete']
        )->name('item.delete');

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [App\Http\Controllers\Action\ItemController::class, 'update']
        );

    }
);
