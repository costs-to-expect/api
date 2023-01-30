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
            'resource-types/{resource_type_id}/categories',
            [App\Http\Controllers\View\CategoryController::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/categories',
            [App\Http\Controllers\View\CategoryController::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [App\Http\Controllers\View\CategoryController::class, 'show']
        )->name('category.show');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [App\Http\Controllers\View\CategoryController::class, 'optionsShow']
        );


        Route::get(
            'summary/resource-types/{resource_type_id}/categories',
            [App\Http\Controllers\Summary\View\CategoryController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories',
            [App\Http\Controllers\Summary\View\CategoryController::class, 'optionsIndex']
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
            'resource-types/{resource_type_id}/categories',
            [App\Http\Controllers\Manage\CategoryController::class, 'create']
        )->name('category.create');

        Route::delete(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [App\Http\Controllers\Manage\CategoryController::class, 'delete']
        )->name('category.delete');

        Route::patch(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [App\Http\Controllers\Manage\CategoryController::class, 'update']
        )->name('category.update');

    }
);
