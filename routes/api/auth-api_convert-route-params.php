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
        Route::get('auth/user', 'PassportController@user');

        Route::post(
            'categories',
            'CategoryController@create'
        );
        Route::post(
            'categories/{category_id}/sub_categories',
            'SubCategoryController@create'
        );
        Route::post(
            'resource_types',
            'ResourceTypeController@create'
        );
        Route::post(
            'resource_types/{resource_type_id}/resources',
            'ResourceController@create'
        );
        Route::post(
            'resource_types/{resource_type_id}/resources/{resource_id}/items',
            'ItemController@create'
        );
        Route::post(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category',
            'ItemCategoryController@create'
        );
        Route::post(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category',
            'ItemSubCategoryController@create'
        );

        Route::delete(
            'categories/{category_id}',
            'CategoryController@delete'
        );
        Route::delete(
            'categories/{category_id}/sub_categories/{sub_category_id}',
            'SubCategoryController@delete'
        );
        Route::delete(
            'resource_types/{resource_type_id}',
            'ResourceTypeController@delete'
        );
        Route::delete(
            'resource_types/{resource_type_id}/resources/{resource_id}',
            'ResourceController@delete'
        );
        Route::delete(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@delete'
        );
        Route::delete(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}',
            'ItemCategoryController@delete'
        );
        Route::delete(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category/{item_sub_category_id}',
            'ItemSubCategoryController@delete'
        );

        Route::patch(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@update'
        );
    }
);
