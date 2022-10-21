<?php

use App\Http\Controllers\Authentication;
use App\Http\Controllers\ItemCategoryManage;
use App\Http\Controllers\ItemDataManage;
use App\Http\Controllers\ItemLogManage;
use App\Http\Controllers\ItemManage;
use App\Http\Controllers\ItemPartialTransferManage;
use App\Http\Controllers\ItemSubcategoryManage;
use App\Http\Controllers\ItemTransferManage;
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
            [Authentication::class, 'user']
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
            [ItemManage::class, 'create']
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [ItemCategoryManage::class, 'create']
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [ItemSubcategoryManage::class, 'create']
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [ItemDataManage::class, 'create']
        )->name('item-data.create');

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log',
            [ItemLogManage::class, 'create']
        )->name('item-log.create');

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            [ItemPartialTransferManage::class, 'transfer']
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            [ItemTransferManage::class, 'transfer']
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
            [ItemPartialTransferManage::class, 'delete']
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceManage::class, 'delete']
        )->name('resource.delete');

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [ItemManage::class, 'delete']
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [ItemCategoryManage::class, 'delete']
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [ItemSubcategoryManage::class, 'delete']
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [ItemDataManage::class, 'delete']
        )->name('item-data.delete');

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
            [ItemManage::class, 'update']
        );

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [ItemDataManage::class, 'update']
        )->name('item-data.update');
    }
);
