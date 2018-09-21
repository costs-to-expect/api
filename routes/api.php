<?php

use Illuminate\Support\Facades\Config;
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

Route::post('auth/login', 'PassportController@login')->prefix(Config::get('api.version.prefix'));
/**
 * Disabled because there are only currently going to be two users
 */
//Route::post('auth/register', 'PassportController@register')->prefix(Config::get('api.version.prefix'));


Route::get('', function () {
    return redirect('/' . Config::get('api.version.prefix'));
});

Route::group(
    [
        'prefix' => Config::get('api.version.prefix'),
        'middleware' => [
            'convert.hash.ids',
            'log.request'
        ]
    ],
    function () {
        Route::get('', 'IndexController@index');
        Route::options('', 'IndexController@optionsIndex');

        Route::get(
            'categories',
            'CategoryController@index'
        );
        Route::options(
            'categories',
            'CategoryController@optionsIndex'
        );
        Route::get(
            'categories/{category_id}',
            'CategoryController@show'
        );
        Route::options(
            'categories/{category_id}',
            'CategoryController@optionsShow'
        );

        Route::get(
            'categories/{category_id}/sub_categories',
            'SubCategoryController@index'
        );
        Route::options(
            'categories/{category_id}/sub_categories',
            'SubCategoryController@optionsIndex'
        );
        Route::get(
            'categories/{category_id}/sub_categories/{sub_category_id}',
            'SubCategoryController@show'
        );
        Route::options(
            'categories/{category_id}/sub_categories/{sub_category_id}',
            'SubCategoryController@optionsShow'
        );

        Route::get(
            'resource_types',
            'ResourceTypeController@index'
        );
        Route::options(
            'resource_types',
            'ResourceTypeController@optionsIndex'
        );
        Route::get(
            'resource_types/{resource_type_id}',
            'ResourceTypeController@show'
        );
        Route::options(
            'resource_types/{resource_type_id}',
            'ResourceTypeController@optionsShow'
        );

        Route::get(
            'resource_types/{resource_type_id}/resources',
            'ResourceController@index'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources',
            'ResourceController@optionsIndex'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}',
            'ResourceController@show'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}',
            'ResourceController@optionsShow'
        );

        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/items',
            'ItemController@index'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/items',
            'ItemController@optionsIndex'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@show'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemController@optionsShow'
        );

        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category',
            'ItemCategoryController@index'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category',
            'ItemCategoryController@optionsIndex'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}',
            'ItemCategoryController@show'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}',
            'ItemCategoryController@optionsShow'
        );

        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category',
            'ItemSubCategoryController@index'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category',
            'ItemSubCategoryController@optionsIndex'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category/{item_sub_category_id}',
            'ItemSubCategoryController@show'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/items/{item_id}/category/{item_category_id}/sub_category/{item_sub_category_id}',
            'ItemSubCategoryController@optionsShow'
        );

        // Summary end points
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/categories',
            'SummaryController@categories'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/categories',
            'SummaryController@optionsCategories'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}',
            'SummaryController@category'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}',
            'SummaryController@optionsCategory'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}/sub_categories',
            'SummaryController@subCategories'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}/sub_categories',
            'SummaryController@optionsSubCategories'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}/sub_categories/{sub_category_id}',
            'SummaryController@subCategory'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/categories/{category_id}/sub_categories/{sub_category_id}',
            'SummaryController@optionsSubCategory'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/tco',
            'SummaryController@tco'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/tco',
            'SummaryController@optionsTco'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/years',
            'SummaryController@years'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/years',
            'SummaryController@optionsYears'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}',
            'SummaryController@year'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}',
            'SummaryController@optionsYear'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}/months',
            'SummaryController@months'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}/months',
            'SummaryController@optionsMonths'
        );
    }
);

Route::group(
    [
        'middleware' => [
            'auth:api',
            'convert.hash.ids'
        ],
        'prefix' => Config::get('api.version.prefix'),
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
    }
);
