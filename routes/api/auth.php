<?php

use App\Http\Controllers\Authentication;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
    ],
    function () {
        Route::get(
            'auth/check',
            [Authentication::class, 'check']
        )->name('auth.check');

        Route::post(
            'auth/create-new-password',
            [Authentication::class, 'createNewPassword']
        )->name('auth.create-new-password');

        Route::post(
            'auth/forgot-password',
            [Authentication::class, 'forgotPassword']
        )->name('auth.forgot-password');

        Route::post(
            'auth/login',
            [Authentication::class, 'login']
        )->name('auth.login');


        if (Config::get('api.app.config.registrations') === true) {
            Route::post(
                'auth/register',
                'Authentication@register'
            )->name('auth.register');

            Route::post(
                'auth/create-password',
                [Authentication::class, 'createPassword']
            )->name('auth.create-password');
        }
    }
);

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
        'middleware' => [
            'auth:sanctum'
        ]
    ],
    static function () {
        Route::post(
            'auth/update-password',
            [Authentication::class, 'updatePassword']
        )->name('auth.update-password');

        Route::post(
            'auth/update-profile',
            [Authentication::class, 'updateProfile']
        )->name('auth.update-profile');

        Route::get(
            'auth/user',
            [Authentication::class, 'user']
        )->name('auth.user');
    }
);

