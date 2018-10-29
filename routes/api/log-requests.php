<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.version.prefix'),
        'middleware' => [
            'log.requests'
        ]
    ],
    function () {
        Route::get('', 'IndexController@index');
        Route::options('', 'IndexController@optionsIndex');

        Route::get('changelog', 'IndexController@changeLog');
        Route::options('changelog', 'IndexController@optionsChangeLog');
    }
);
