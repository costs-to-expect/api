<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.version.prefix')
    ],
    function () {
        Route::get('request/error-log', 'RequestController@errorLog');
        Route::options('request/error-log', 'RequestController@optionsErrorLog');
        Route::get('request/log', 'RequestController@log');
        Route::options('request/log', 'RequestController@optionsLog');
        Route::get('request/log/monthly-requests', 'RequestController@monthlyRequests');
        Route::options('request/log/monthly-requests', 'RequestController@optionsMonthlyRequests');

        Route::post('request/error-log', 'RequestController@createErrorLog');
    }
);
