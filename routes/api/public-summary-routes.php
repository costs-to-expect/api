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
            [Summary\ResourceTypeView::class, 'index']
        );

        Route::options(
            'summary/resource-types',
            [Summary\ResourceTypeView::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/categories',
            [Summary\CategoryView::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories',
            [Summary\CategoryView::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [Summary\SubcategoryView::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [Summary\SubcategoryView::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources',
            [Summary\ResourceView::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources',
            [Summary\ResourceView::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/items',
            [Summary\ResourceTypeItemView::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/items',
            [Summary\ResourceTypeItemView::class, 'optionsIndex']
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            [Summary\ItemView::class, 'index']
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            [Summary\ItemView::class, 'optionsIndex']
        );
    }
);
