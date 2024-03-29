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
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [App\Http\Controllers\View\SubcategoryController::class, 'index']
        )->name('subcategory.list');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [App\Http\Controllers\View\SubcategoryController::class, 'optionsIndex']
        )->name('subcategory.list.options');

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [App\Http\Controllers\View\SubcategoryController::class, 'show']
        )->name('subcategory.show');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [App\Http\Controllers\View\SubcategoryController::class, 'optionsShow']
        )->name('subcategory.show.options');


        Route::get(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [App\Http\Controllers\Summary\View\SubcategoryController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [App\Http\Controllers\Summary\View\SubcategoryController::class, 'optionsIndex']
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
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [App\Http\Controllers\Action\SubcategoryController::class, 'create']
        )->name('subcategory.create');

        Route::delete(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [App\Http\Controllers\Action\SubcategoryController::class, 'delete']
        )->name('subcategory.delete');

        Route::patch(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [App\Http\Controllers\Action\SubcategoryController::class, 'update']
        )->name('subcategory.update');

    }
);
