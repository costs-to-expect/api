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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [App\Http\Controllers\View\ItemSubcategoryController::class, 'index']
        )->name('item-subcategory.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [App\Http\Controllers\View\ItemSubcategoryController::class, 'optionsIndex']
        )->name('item-subcategory.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [App\Http\Controllers\View\ItemSubcategoryController::class, 'show']
        )->name('item-subcategory.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [App\Http\Controllers\View\ItemSubcategoryController::class, 'optionsShow']
        )->name('item-subcategory.show.options');

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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [App\Http\Controllers\Action\ItemSubcategoryController::class, 'create']
        )->name('item-subcategory.create');

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [App\Http\Controllers\Action\ItemSubcategoryController::class, 'delete']
        )->name('item-subcategory.delete');

    }
);