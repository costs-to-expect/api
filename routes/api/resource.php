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
            'resource-types/{resource_type_id}/resources',
            [App\Http\Controllers\View\ResourceController::class, 'index']
        )->name('resource.list');

        Route::options(
            'resource-types/{resource_type_id}/resources',
            [App\Http\Controllers\View\ResourceController::class, 'optionsIndex']
        )->name('resource.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [App\Http\Controllers\View\ResourceController::class, 'show']
        )->name('resource.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [App\Http\Controllers\View\ResourceController::class, 'optionsShow']
        )->name('resource.show.options');


        Route::get(
            'summary/resource-types/{resource_type_id}/resources',
            [App\Http\Controllers\Summary\View\ResourceController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources',
            [App\Http\Controllers\Summary\View\ResourceController::class, 'optionsIndex']
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
            'resource-types/{resource_type_id}/resources',
            [App\Http\Controllers\Manage\ResourceController::class, 'create']
        )->name('resource.create');

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [App\Http\Controllers\Manage\ResourceController::class, 'delete']
        )->name('resource.delete');

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [App\Http\Controllers\Manage\ResourceController::class, 'update']
        )->name('resource.update');

    }
);
