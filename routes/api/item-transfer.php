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

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            [App\Http\Controllers\View\ItemTransferController::class, 'optionsTransfer']
        );

        Route::options(
            'resource-types/{resource_type_id}/transfers',
            [App\Http\Controllers\View\ItemTransferController::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers',
            [App\Http\Controllers\View\ItemTransferController::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            [App\Http\Controllers\View\ItemTransferController::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            [App\Http\Controllers\View\ItemTransferController::class, 'show']
        );

    }
);

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
        'middleware' => [
            'auth:sanctum',
            'convert.route.parameters'
        ]
    ],
    static function () {

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            [App\Http\Controllers\Action\ItemTransferController::class, 'transfer']
        );

    }
);
