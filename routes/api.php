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
        Route::get('resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}', 'ItemController@show');

        Route::get('user', 'PassportController@user');
    }
);

Route::group(
    ['middleware' => 'auth:api'],
    function () {
        Route::get('user', 'PassportController@user');
    }
);
