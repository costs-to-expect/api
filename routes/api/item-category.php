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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [App\Http\Controllers\View\ItemCategoryController::class, 'index']
        )->name('item.categories.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [App\Http\Controllers\View\ItemCategoryController::class, 'optionsIndex']
        )->name('item.categories.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [App\Http\Controllers\View\ItemCategoryController::class, 'show']
        )->name('item.categories.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [App\Http\Controllers\View\ItemCategoryController::class, 'optionsShow']
        )->name('item.categories.show.options');

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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [App\Http\Controllers\Action\ItemCategoryController::class, 'create']
        )->name('item.categories.create');

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [App\Http\Controllers\Action\ItemCategoryController::class, 'delete']
        )->name('item.categories.delete');

    }
);
