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
            'summary/categories',
            'SummaryCategoryController@index'
        );

        Route::options(
            'summary/categories',
            'SummaryCategoryController@optionsIndex'
        );

        Route::get(
            'summary/resource-types',
            'SummaryResourceTypeController@index'
        );

        Route::options(
            'summary/resource-types',
            'SummaryResourceTypeController@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources',
            'SummaryResourceController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources',
            'SummaryResourceController@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/items',
            'SummaryResourceTypeItemController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/items',
            'SummaryResourceTypeItemController@optionsIndex'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            'SummaryItemController@index'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/items',
            'SummaryItemController@optionsIndex'
        );

        Route::get(
            'summary/request/access-log',
            'SummaryRequestController@AccessLog'
        );

        Route::options(
            'summary/request/access-log',
            'SummaryRequestController@optionsAccessLog'
        );
    }
);
