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
            'summary/resource-types',
            'Summary\ResourceTypeView@index'
        );

        Route::options(
            'summary/resource-types',
            'Summary\ResourceTypeView@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/categories',
            'Summary\CategoryView@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories',
            'Summary\CategoryView@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            'Summary\SubcategoryView@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            'Summary\SubcategoryView@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources',
            'Summary\ResourceView@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources',
            'Summary\ResourceView@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/items',
            'Summary\ResourceTypeItemView@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/items',
            'Summary\ResourceTypeItemView@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            'Summary\ItemView@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            'Summary\ItemView@optionsIndex'
        );
    }
);
