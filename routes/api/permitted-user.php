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
            'resource-types/{resource_type_id}/permitted-users',
            [App\Http\Controllers\View\PermittedUserController::class, 'index']
        )->name('permitted-user.list');

        Route::options(
            'resource-types/{resource_type_id}/permitted-users',
            [App\Http\Controllers\View\PermittedUserController::class, 'optionsIndex']
        )->name('permitted-user.list.options');

        Route::get(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [App\Http\Controllers\View\PermittedUserController::class, 'show']
        )->name('permitted-user.show');

        Route::options(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [App\Http\Controllers\View\PermittedUserController::class, 'optionsShow']
        )->name('permitted-user.show.options');

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
            'resource-types/{resource_type_id}/permitted-users',
            [App\Http\Controllers\Manage\PermittedUserController::class, 'create']
        )->name('permitted-user.create');

        Route::delete(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [App\Http\Controllers\Manage\PermittedUserController::class, 'delete']
        )->name('permitted-user.delete');

    }
);
