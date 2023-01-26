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
            'resource-types',
            [\App\Http\Controllers\View\ResourceTypeController::class, 'index']
        )->name('resource-type.list');

        Route::options(
            'resource-types',
            [\App\Http\Controllers\View\ResourceTypeController::class, 'optionsIndex']
        )->name('resource-type.list.options');

        Route::get(
            'resource-types/{resource_type_id}',
            [\App\Http\Controllers\View\ResourceTypeController::class, 'show']
        )->name('resource-type.show');

        Route::options(
            'resource-types/{resource_type_id}',
            [\App\Http\Controllers\View\ResourceTypeController::class, 'optionsShow']
        )->name('resource-type.show.options');

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
            'resource-types',
            [App\Http\Controllers\Manage\ResourceTypeController::class, 'create']
        )->name('resource-type.create');

        Route::delete(
            'resource-types/{resource_type_id}',
            [App\Http\Controllers\Manage\ResourceTypeController::class, 'delete']
        )->name('resource-type.delete');

        Route::patch(
            'resource-types/{resource_type_id}',
            [App\Http\Controllers\Manage\ResourceTypeController::class, 'update']
        )->name('resource-type.update');

    }
);
