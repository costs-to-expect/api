<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
        'middleware' => [
            'convert.route.parameters',
            'convert.get.parameters'
        ]
    ],
    static function () {

        Route::get(
            'resource-types/{resource_type_id}/items',
            [App\Http\Controllers\View\ResourceTypeItemController::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/items',
            [App\Http\Controllers\View\ResourceTypeItemController::class, 'optionsIndex']
        );

    }
);
