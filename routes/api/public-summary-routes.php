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
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/years',
            'SummaryPeriodController@years'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/years',
            'SummaryPeriodController@optionsYears'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/years/{year}',
            'SummaryPeriodController@year'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/years/{year}',
            'SummaryPeriodController@optionsYear'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/years/{year}/months',
            'SummaryPeriodController@months'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/years/{year}/months',
            'SummaryPeriodController@optionsMonths'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/years/{year}/months/{month}',
            'SummaryPeriodController@month'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/years/{year}/months/{month}',
            'SummaryPeriodController@optionsMonth'
        );

        Route::get(
            'summary/request/access-log/monthly',
            'SummaryRequestController@monthlyAccessLog'
        );

        Route::options(
            'summary/request/access-log/monthly',
            'SummaryRequestController@optionsMonthlyAccessLog'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/tco',
            'SummaryController@tco'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/tco',
            'SummaryController@optionsTco'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/categories',
            'SummaryCategoryController@categories'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/categories',
            'SummaryCategoryController@optionsCategories'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/categories/{category_id}',
            'SummaryCategoryController@category'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/categories/{category_id}',
            'SummaryCategoryController@optionsCategory'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/categories/{category_id}/subcategories',
            'SummaryCategoryController@subCategories'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/categories/{category_id}/subcategories',
            'SummaryCategoryController@optionsSubCategories'
        );

        Route::get(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/categories/{category_id}/subcategories/{sub_category_id}',
            'SummaryCategoryController@subCategory'
        );

        Route::options(
            'summary/resource-types/{resource_type_id}/resources/{resource_id}/categories/{category_id}/subcategories/{sub_category_id}',
            'SummaryCategoryController@optionsSubCategory'
        );
    }
);
