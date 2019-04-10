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
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            'SummaryItemController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            'SummaryItemController@optionsIndex'
        );

        Route::get(
            'summary/request/access-log/monthly',
            'SummaryRequestController@monthlyAccessLog'
        );

        Route::options(
            'summary/request/access-log/monthly',
            'SummaryRequestController@optionsMonthlyAccessLog'
        );
    }
);
