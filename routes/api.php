<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'PassportController@login');
Route::post('register', 'PassportController@register');

Route::group(
    [],
    function () {
        Route::get('categories', 'CategoryController@index');
        Route::options('categories', 'CategoryController@optionsIndex');
        Route::get('categories/{category_id}', 'CategoryController@show');
        Route::options('categories/{category_id}', 'CategoryController@optionsShow');

        Route::get('categories/{category_id}/sub_categories', 'SubCategoryController@index');
        Route::options('categories/{category_id}/sub_categories', 'SubCategoryController@optionsIndex');
        Route::get('categories/{category_id}/sub_categories/{sub_category_id}', 'SubCategoryController@show');
        Route::options('categories/{category_id}/sub_categories/{sub_category_id}', 'SubCategoryController@optionsShow');

        Route::get('resource_types', 'ResourceTypeController@index');
        Route::options('resource_types', 'ResourceTypeController@optionsIndex');
        Route::get('resource_types/{resource_type_id}', 'ResourceTypeController@show');
        Route::options('resource_types/{resource_type_id}', 'ResourceTypeController@optionsShow');

        Route::get('resource_types/{resource_type_id}/resources', 'ResourceController@index');
        Route::options('resource_types/{resource_type_id}/resources', 'ResourceController@optionsIndex');
        Route::get('resource_types/{resource_type_id}/resources/{resource_id}', 'ResourceController@show');
        Route::options('resource_types/{resource_type_id}/resources/{resource_id}', 'ResourceController@optionsShow');

        Route::get('resource_types/{resource_type_id}/resources/{resource_id}/items', 'ItemController@index');
        Route::options('resource_types/{resource_type_id}/resources/{resource_id}/items', 'ItemController@optionsIndex');
        Route::get('resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}', 'ItemController@show');
        Route::options('resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}', 'ItemController@optionsShow');
    }
);

Route::group(
    ['middleware' => 'auth:api'],
    function () {
        Route::get('user', 'PassportController@user');

        Route::post('categories', 'CategoryController@create');
        Route::post('categories/{category_id}/sub_categories', 'SubCategoryController@create');
        Route::post('resource_types', 'ResourceTypeController@create');
        Route::post('resource_types/{resource_type_id}/resources', 'ResourceController@create');
        Route::post('resource_types/{resource_type_id}/resources/{resource_id}/items', 'ItemController@create');

        Route::delete('categories/{category_id}', 'CategoryController@delete');
        Route::delete('categories/{category_id}/sub_categories/{sub_category_id}', 'SubCategoryController@delete');
        Route::delete('resource_types/{resource_type_id}', 'ResourceTypeController@delete');
        Route::delete('resource_types/{resource_type_id}/resources/{resource_id}', 'ResourceController@delete');
        Route::delete('resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}', 'ItemController@delete');

        Route::patch('categories/{category_id}', 'CategoryController@update');
        Route::patch('categories/{category_id}/sub_categories/{sub_category_id}', 'SubCategoryController@update');
        Route::patch('resource_types/{resource_type_id}', 'ResourceTypeController@update');
        Route::patch('resource_types/{resource_type_id}/resources/{resource_id}', 'ResourceController@update');
    }
);
