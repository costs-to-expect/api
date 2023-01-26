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
            'resource-types/{resource_type_id}/partial-transfers',
            [App\Http\Controllers\View\ItemPartialTransferController::class, 'index']
        )->name('partial-transfers.list');

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers',
            [App\Http\Controllers\View\ItemPartialTransferController::class, 'optionsIndex']
        )->name('partial-transfers.list.options');

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            [App\Http\Controllers\View\ItemPartialTransferController::class, 'show']
        )->name('partial-transfers.show');

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            [App\Http\Controllers\View\ItemPartialTransferController::class, 'optionsShow']
        )->name('partial-transfers.show.options');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            [App\Http\Controllers\View\ItemPartialTransferController::class, 'optionsTransfer']
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
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            [App\Http\Controllers\Manage\ItemPartialTransferController::class, 'transfer']
        );

        Route::delete(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            [App\Http\Controllers\Manage\ItemPartialTransferController::class, 'delete']
        );

    }
);
