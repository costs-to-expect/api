<?php

use App\Http\Controllers\Summary;
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
            'summary/resource-types',
            [Summary\View\ResourceTypeController::class, 'index']
        );

        Route::options(
            'summary/resource-types',
            [Summary\View\ResourceTypeController::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/categories',
            [Summary\View\CategoryController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories',
            [Summary\View\CategoryController::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [Summary\View\SubcategoryController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [Summary\View\SubcategoryController::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources',
            [Summary\View\ResourceController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources',
            [Summary\View\ResourceController::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/items',
            [Summary\View\ResourceTypeItemController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/items',
            [Summary\View\ResourceTypeItemController::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            [Summary\View\ItemController::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            [Summary\View\ItemController::class, 'optionsIndex']
        );
    }
);
