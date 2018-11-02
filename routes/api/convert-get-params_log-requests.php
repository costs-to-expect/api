<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.version.prefix'),
        'middleware' => [
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
            'resource_types',
            'ResourceTypeController@index'
        );
        Route::options(
            'resource_types',
            'ResourceTypeController@optionsIndex'
        );
    }
);
