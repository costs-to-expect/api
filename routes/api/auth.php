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

        Route::options(
            'auth/create-new-password',
            [Authentication::class, 'optionsCreateNewPassword']
        );

        Route::post(
            'auth/forgot-password',
            [Authentication::class, 'forgotPassword']
        )->name('auth.forgot-password');

        Route::options(
            'auth/forgot-password',
            [Authentication::class, 'optionsForgotPassword']
        );

        Route::post(
            'auth/login',
            [Authentication::class, 'login']
        )->name('auth.login');

        Route::options(
            'auth/login',
            [Authentication::class, 'optionsLogin']);

        Route::get(
            'auth/logout',
            [Authentication::class, 'logout']
        )->name('auth.logout');

        Route::options(
            'auth/update-password',
            [Authentication::class, 'optionsUpdatePassword']
        );

        Route::options(
            'auth/update-profile',
            [Authentication::class, 'optionsUpdateProfile']
        );

        Route::options(
            'auth/user',
            [Authentication::class, 'optionsUser']
        );

        Route::options(
            'auth/user/tokens',
            [Authentication::class, 'optionsTokens']
        );

        Route::options(
            'auth/user/tokens/{token_id}',
            [Authentication::class, 'optionsToken']
        );

        Route::options(
            'auth/check',
            [Authentication::class, 'optionsCheck']
        );

        if (Config::get('api.app.config.registrations') === true) {
            Route::post(
                'auth/register',
                [Authentication::class, 'register']
            )->name('auth.register');

            Route::options(
                'auth/register',
                [Authentication::class, 'optionsRegister']
            );

            Route::post(
                'auth/create-password',
                [Authentication::class, 'createPassword']
            )->name('auth.create-password');

            Route::options(
                'auth/create-password',
                [Authentication::class, 'optionsCreatePassword']
            );
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

        Route::get(
            'auth/user/tokens',
            [Authentication::class, 'tokens']
        )->name('auth.user.token.list');

        Route::get(
            'auth/user/tokens/{token_id}',
            [Authentication::class, 'token']
        )->name('auth.user.token.show');

        Route::delete(
            'auth/user/tokens/{token_id}',
            [Authentication::class, 'deleteToken']
        )->name('auth.user.token.delete');
    }
);

