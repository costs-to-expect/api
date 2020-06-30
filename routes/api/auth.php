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
            'PassportView@login'
        );

        Route::get(
            'auth/user',
            'PassportView@user'
        );

        Route::get(
            'auth/check',
            'PassportView@check'
        );

        if (Config::get('api.app.config.registrations') === true) {
            Route::post(
                'auth/register',
                'PassportView@register'
            );
        }
    }
);
