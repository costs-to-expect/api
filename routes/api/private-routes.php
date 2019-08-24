<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.version.prefix'),
        'middleware' => [
            'auth:api',
            'convert.route.parameters'
        ]
    ],
    function () {
        Route::get(
            'auth/user',
            'PassportController@user'
        );

        Route::post(
            'categories',
            'CategoryController@create'
        );

        Route::post(
            'categories/{category_id}/subcategories',
            'SubcategoryController@create'
        );

        Route::post(
            'resource-types',
            'ResourceTypeController@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources',
            'ResourceController@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            'ItemController@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category',
            'ItemCategoryController@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory',
            'ItemSubCategoryController@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            'ItemTransferController@transfer'
        );

        Route::delete(
            'categories/{category_id}',
            'CategoryController@delete'
        );

        Route::delete(
            'categories/{category_id}/sub_categories/{sub_category_id}',
            'SubcategoryController@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}',
            'ResourceTypeController@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            'ResourceController@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}',
            'ItemCategoryController@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory/{item_sub_category_id}',
            'ItemSubCategoryController@delete'
        );

        Route::patch(
            'categories/{category_id}',
            'CategoryController@update'
        );

        Route::patch(
            'categories/{category_id}/subcategories/{sub_category_id}',
            'SubcategoryController@update'
        );

        Route::patch(
            'resource-types/{resource_type_id}',
            'ResourceTypeController@update'
        );

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@update'
        );
    }
);
