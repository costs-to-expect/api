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
            'resource_types/{resource_type_id}/resources/{resource_id}/expanded_summary/categories',
            'ExpandedSummaryController@categories'
        );

        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/expanded_summary/categories',
            'ExpandedSummaryController@optionsCategories'
        );

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

        Route::get(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}/months/{month}',
            'SummaryController@month'
        );

        Route::options(
            'resource_types/{resource_type_id}/resources/{resource_id}/summary/years/{year}/months/{month}',
            'SummaryController@optionsMonth'
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
