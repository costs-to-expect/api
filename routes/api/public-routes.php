<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
        'middleware' => [
            'convert.route.parameters',
            'convert.get.parameters',
            'log.requests'
        ]
    ],
    function () {
        // Root of the API and CHANGELOG
        Route::get(
            '',
            'IndexController@index'
        );

        Route::options(
            '',
            'IndexController@optionsIndex'
        );

        Route::get(
            'changelog',
            'IndexController@changeLog'
        );

        Route::options(
            'changelog',
            'IndexController@optionsChangeLog'
        );

        Route::get(
            'item-types',
            'ItemTypeController@index'
        );

        Route::options(
            'item-types',
            'ItemTypeController@optionsIndex'
        );

        Route::get(
            'item-types/{item_type_id}',
            'ItemTypeController@show'
        );

        Route::options(
            'item-types/{item_type_id}',
            'ItemTypeController@optionsShow'
        );

        Route::get(
            'resource-types',
            'ResourceTypeController@index'
        );

        Route::options(
            'resource-types',
            'ResourceTypeController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}',
            'ResourceTypeController@show'
        );

        Route::options(
            'resource-types/{resource_type_id}',
            'ResourceTypeController@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/categories',
            'CategoryController@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/categories',
            'CategoryController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}',
            'CategoryController@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}',
            'CategoryController@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            'SubcategoryController@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            'SubcategoryController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            'SubcategoryController@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            'SubcategoryController@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/items',
            'ResourceTypeItemController@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/items',
            'ResourceTypeItemController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers',
            'ItemPartialTransferController@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers',
            'ItemPartialTransferController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            'ItemPartialTransferController@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            'ItemPartialTransferController@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/permitted-users',
            'PermittedUserController@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/permitted-users',
            'PermittedUserController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources',
            'ResourceController@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources',
            'ResourceController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            'ResourceController@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            'ResourceController@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            'ItemController@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            'ItemController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@optionsShow'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            'ItemPartialTransferController@optionsTransfer'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            'ItemTransferController@optionsTransfer'
        );

        /*This route needs to be removed*/
        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category',
            'ItemCategoryController@index'
        );

        /*This route needs to be removed*/
        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category',
            'ItemCategoryController@optionsIndex'
        );

        /*This route needs to be removed*/
        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}',
            'ItemCategoryController@show'
        );

        /*This route needs to be removed*/
        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}',
            'ItemCategoryController@optionsShow'
        );

        /*This route needs to be removed*/
        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory',
            'ItemSubcategoryController@index'
        );

        /*This route needs to be removed*/
        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory',
            'ItemSubcategoryController@optionsIndex'
        );

        /*This route needs to be removed*/
        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory/{item_subcategory_id}',
            'ItemSubcategoryController@show'
        );

        /*This route needs to be removed*/
        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/subcategory/{item_subcategory_id}',
            'ItemSubcategoryController@optionsShow'
        );

        Route::options(
            'resource-types/{resource_type_id}/transfers',
            'ItemTransferController@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers',
            'ItemTransferController@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            'ItemTransferController@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            'ItemTransferController@show'
        );

        // Request access and error logs
        Route::options(
            'request/error-log',
            'RequestController@optionsErrorLog'
        );

        Route::get(
            'request/error-log',
            'RequestController@errorLog'
        );

        Route::post(
            'request/error-log',
            'RequestController@createErrorLog'
        );

        Route::get(
            'request/access-log',
            'RequestController@accessLog'
        );

        Route::options(
            'request/access-log',
            'RequestController@optionsAccessLog'
        );
    }
);
