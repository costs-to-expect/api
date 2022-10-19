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
            [Authentication::class, 'optionsLogin']
        )->name('auth.login.options');

        Route::get(
            'auth/logout',
            [Authentication::class, 'logout']
        )->name('auth.logout');

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
            )->name('auth.register.options');

            Route::post(
                'auth/create-password',
                [Authentication::class, 'createPassword']
            )->name('auth.create-password');

            Route::options(
                'auth/create-password',
                [Authentication::class, 'optionsCreatePassword']
            )->name('auth.create-password.options');
        }
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

        Route::get(
            'auth/user/permitted-resource-types',
            [Authentication::class, 'permittedResourceTypes']
        )->name('auth.user.permitted-resource-types.list');

        Route::options(
            'auth/user/permitted-resource-types',
            [Authentication::class, 'optionsPermittedResourceTypes']
        )->name('auth.user.permitted-resource-types.list.options');

        Route::get(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}',
            [Authentication::class, 'permittedResourceType']
        )->name('auth.user.permitted-resource-types.show');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}',
            [Authentication::class, 'optionsPermittedResourceType']
        )->name('auth.user.permitted-resource-types.show.options');

        Route::get(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources',
            [Authentication::class, 'permittedResourceTypesResources']
        )->name('auth.user.permitted-resource-types-resources.list');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources',
            [Authentication::class, 'optionsPermittedResourceTypeResources']
        )->name('auth.user.permitted-resource-types-resources.list.options');

        Route::get(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}',
            [Authentication::class, 'permittedResourceTypesResources']
        )->name('auth.user.permitted-resource-types-resources.show');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}',
            [Authentication::class, 'optionsPermittedResourceTypeResource']
        )->name('auth.user.permitted-resource-types-resources.show.options');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}/request-delete',
            [Authentication::class, 'optionsRequestResourceDelete']
        )->name('auth.user.request-resource-delete.options');

        Route::post(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}/request-delete',
            [Authentication::class, 'requestResourceDelete']
        )->name('auth.user.request-resource-delete');
    }
);
