<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
    ],
    function () {
        Route::get(
            'auth/check',
            [App\Http\Controllers\View\AuthenticationController::class, 'check']
        )->name('auth.check');

        Route::post(
            'auth/create-new-password',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'createNewPassword']
        )->name('auth.create-new-password');

        Route::options(
            'auth/create-new-password',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsCreateNewPassword']
        );

        Route::post(
            'auth/forgot-password',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'forgotPassword']
        )->name('auth.forgot-password');

        Route::options(
            'auth/forgot-password',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsForgotPassword']
        );

        Route::post(
            'auth/login',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'login']
        )->name('auth.login');

        Route::options(
            'auth/login',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsLogin']
        )->name('auth.login.options');

        Route::get(
            'auth/logout',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'logout']
        )->name('auth.logout');

        Route::options(
            'auth/check',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsCheck']
        );

        if (Config::get('api.app.config.registrations') === true) {
            Route::post(
                'auth/register',
                [App\Http\Controllers\Manage\AuthenticationController::class, 'register']
            )->name('auth.register');

            Route::options(
                'auth/register',
                [App\Http\Controllers\View\AuthenticationController::class, 'optionsRegister']
            )->name('auth.register.options');

            Route::post(
                'auth/create-password',
                [App\Http\Controllers\Manage\AuthenticationController::class, 'createPassword']
            )->name('auth.create-password');

            Route::options(
                'auth/create-password',
                [App\Http\Controllers\View\AuthenticationController::class, 'optionsCreatePassword']
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
        Route::options(
            'auth/update-password',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsUpdatePassword']
        )->name('auth.update-password.options');

        Route::post(
            'auth/update-password',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'updatePassword']
        )->name('auth.update-password');

        Route::options(
            'auth/update-profile',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsUpdateProfile']
        )->name('auth.update-profile.options');

        Route::post(
            'auth/update-profile',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'updateProfile']
        )->name('auth.update-profile');

        Route::options(
            'auth/user',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsUser']
        )->name('auth.user.options');

        Route::get(
            'auth/user',
            [App\Http\Controllers\View\AuthenticationController::class, 'user']
        );

        Route::options(
            'auth/user/request-delete',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsRequestDelete']
        )->name('auth.user.request-delete.options');

        Route::post(
            'auth/user/request-delete',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'requestDelete']
        )->name('auth.user.request-delete');

        Route::options(
            'auth/user/tokens',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsTokens']
        )->name('auth.user.token.list.options');

        Route::get(
            'auth/user/tokens',
            [App\Http\Controllers\View\AuthenticationController::class, 'tokens']
        )->name('auth.user.token.list');

        Route::options(
            'auth/user/tokens/{token_id}',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsToken']
        )->name('auth.user.token.show.options');

        Route::get(
            'auth/user/tokens/{token_id}',
            [App\Http\Controllers\View\AuthenticationController::class, 'token']
        )->name('auth.user.token.show');

        Route::delete(
            'auth/user/tokens/{token_id}',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'deleteToken']
        )->name('auth.user.token.delete');

        Route::get(
            'auth/user/permitted-resource-types',
            [App\Http\Controllers\View\AuthenticationController::class, 'permittedResourceTypes']
        )->name('auth.user.permitted-resource-types.list');

        Route::options(
            'auth/user/permitted-resource-types',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsPermittedResourceTypes']
        )->name('auth.user.permitted-resource-types.list.options');

        Route::get(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}',
            [App\Http\Controllers\View\AuthenticationController::class, 'permittedResourceType']
        )->name('auth.user.permitted-resource-types.show');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsPermittedResourceType']
        )->name('auth.user.permitted-resource-types.show.options');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/request-delete',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsRequestResourceTypeDelete']
        )->name('auth.user.request-resource-type-delete.options');

        Route::post(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/request-delete',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'requestResourceTypeDelete']
        )->name('auth.user.request-resource-type-delete');

        Route::get(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources',
            [App\Http\Controllers\View\AuthenticationController::class, 'permittedResourceTypesResources']
        )->name('auth.user.permitted-resource-types-resources.list');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsPermittedResourceTypeResources']
        )->name('auth.user.permitted-resource-types-resources.list.options');

        Route::get(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}',
            [App\Http\Controllers\View\AuthenticationController::class, 'permittedResourceTypesResource']
        )->name('auth.user.permitted-resource-types-resources.show');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsPermittedResourceTypeResource']
        )->name('auth.user.permitted-resource-types-resources.show.options');

        Route::options(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}/request-delete',
            [App\Http\Controllers\View\AuthenticationController::class, 'optionsRequestResourceDelete']
        )->name('auth.user.request-resource-delete.options');

        Route::post(
            'auth/user/permitted-resource-types/{permitted_resource_type_id}/resources/{resource_id}/request-delete',
            [App\Http\Controllers\Manage\AuthenticationController::class, 'requestResourceDelete']
        )->name('auth.user.request-resource-delete');
    }
);
