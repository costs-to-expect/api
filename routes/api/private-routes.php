<?php

use App\Http\Controllers\CategoryManage;
use App\Http\Controllers\PermittedUserManage;
use App\Http\Controllers\ResourceManage;
use App\Http\Controllers\ResourceTypeManage;
use App\Http\Controllers\SubcategoryManage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
        'middleware' => [
            'auth:sanctum',
            'convert.route.parameters'
        ]
    ],
    static function () {
        Route::get(
            'auth/user',
            'Authentication@user'
        );

        Route::post(
            'resource-types',
            [ResourceTypeManage::class, 'create']
        )->name('resource-type.create');

        Route::post(
            'resource-types/{resource_type_id}/categories',
            [CategoryManage::class, 'create']
        )->name('category.create');

        Route::post(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [SubcategoryManage::class, 'create']
        )->name('subcategory.create');

        Route::post(
            'resource-types/{resource_type_id}/permitted-users',
            [PermittedUserManage::class, 'create']
        )->name('permitted-user.create');

        Route::post(
            'resource-types/{resource_type_id}/resources',
            [ResourceManage::class, 'create']
        )->name('resource.create');

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            'ItemManage@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            'ItemCategoryManage@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            'ItemSubcategoryManage@create'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            'ItemPartialTransferManage@transfer'
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            'ItemTransferManage@transfer'
        );

        Route::delete(
            'resource-types/{resource_type_id}',
            [ResourceTypeManage::class, 'delete']
        )->name('resource-type.delete');

        Route::delete(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [PermittedUserManage::class, 'delete']
        )->name('permitted-user.delete');

        Route::delete(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [CategoryManage::class, 'delete']
        )->name('category.delete');

        Route::delete(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [SubcategoryManage::class, 'delete']
        )->name('subcategory.delete');

        Route::delete(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            'ItemPartialTransferManage@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceManage::class, 'delete']
        )->name('resource.delete');

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemManage@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            'ItemCategoryManage@delete'
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            'ItemSubcategoryManage@delete'
        );

        Route::patch(
            'resource-types/{resource_type_id}',
            [ResourceTypeManage::class, 'update']
        )->name('resource-type.update');

        Route::patch(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [CategoryManage::class, 'update']
        )->name('category.update');

        Route::patch(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [SubcategoryManage::class, 'update']
        )->name('subcategory.update');

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceManage::class, 'update']
        )->name('resource.update');

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemManage@update'
        );

        Route::get(
            'tools/cache',
            'ToolManage@cache'
        );

        Route::delete(
            'tools/cache',
            'ToolManage@deleteCache'
        );
    }
);
