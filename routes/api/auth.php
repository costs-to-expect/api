<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
    ],
    function () {
        Route::post(
            'auth/login',
            'Authentication@login'
        );

        Route::get(
            'auth/user',
            'Authentication@user'
        );

        Route::get(
            'auth/check',
            'Authentication@check'
        );

        if (Config::get('api.app.config.registrations') === true) {
            Route::post(
                'auth/register',
                'Authentication@register'
            );

            Route::post(
                'auth/create-password',
                [\App\Http\Controllers\Authentication::class, 'createPassword']
            )->name('auth.create-password');
        }
    }
);
