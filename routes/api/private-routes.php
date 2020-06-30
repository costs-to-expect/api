<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
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
            'resource-types',
            'ResourceTypeController@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/categories',
            'CategoryManage@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            'SubcategoryController@create'
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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            'ItemCategoryManage@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            'ItemSubcategoryController@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            'ItemPartialTransferController@transfer'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            'ItemTransferController@transfer'
        );

        Route::delete(
            'resource-types/{resource_type_id}',
            'ResourceTypeController@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/categories/{category_id}',
            'CategoryManage@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            'SubcategoryController@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            'ItemPartialTransferController@delete'
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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            'ItemCategoryManage@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            'ItemSubcategoryController@delete'
        );

        Route::patch(
            'resource-types/{resource_type_id}',
            'ResourceTypeController@update'
        );

        Route::patch(
            'resource-types/{resource_type_id}/categories/{category_id}',
            'CategoryManage@update'
        );

        Route::patch(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            'SubcategoryController@update'
        );

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            'ResourceController@update'
        );

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@update'
        );
    }
);
