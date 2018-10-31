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
            'categories',
            'CategoryController@index'
        );
        Route::options(
            'categories',
            'CategoryController@optionsIndex'
        );
        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/items',
            'ItemController@index'
        );
        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/items',
            'ItemController@optionsIndex'
        );
    }
);
