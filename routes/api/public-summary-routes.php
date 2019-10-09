<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.version.prefix'),
        'middleware' => [
            'convert.route.parameters',
            'convert.get.parameters',
            'log.requests'
        ]
    ],
    function () {

        Route::get(
            'summary/resource-types',
            'Summary\ResourceTypeController@index'
        );

        Route::options(
            'summary/resource-types',
            'Summary\ResourceTypeController@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/categories',
            'Summary\CategoryController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories',
            'Summary\CategoryController@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            'Summary\SubcategoryController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            'Summary\SubcategoryController@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources',
            'Summary\ResourceController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources',
            'Summary\ResourceController@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/items',
            'Summary\ResourceTypeItemController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/items',
            'Summary\ResourceTypeItemController@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            'Summary\ItemController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            'Summary\ItemController@optionsIndex'
        );

        Route::get(
            'summary/request/access-log',
            'Summary\RequestController@AccessLog'
        );

        Route::options(
            'summary/request/access-log',
            'Summary\RequestController@optionsAccessLog'
        );
    }
);
