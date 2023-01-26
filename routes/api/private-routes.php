<?php

use App\Http\Controllers\Manage\ItemCategoryController;
use App\Http\Controllers\Manage\ItemDataController;
use App\Http\Controllers\Manage\ItemLogController;
use App\Http\Controllers\Manage\ItemController;
use App\Http\Controllers\Manage\ItemPartialTransferController;
use App\Http\Controllers\Manage\ItemSubcategoryController;
use App\Http\Controllers\Manage\ItemTransferController;
use App\Http\Controllers\Manage\CategoryController;
use App\Http\Controllers\Manage\PermittedUserController;
use App\Http\Controllers\Manage\ResourceController;
use App\Http\Controllers\Manage\ResourceTypeController;
use App\Http\Controllers\Manage\SubcategoryController;
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
        Route::post(
            'resource-types',
            [ResourceTypeController::class, 'create']
        )->name('resource-type.create');

        Route::post(
            'resource-types/{resource_type_id}/categories',
            [CategoryController::class, 'create']
        )->name('category.create');

        Route::post(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [SubcategoryController::class, 'create']
        )->name('subcategory.create');

        Route::post(
            'resource-types/{resource_type_id}/permitted-users',
            [PermittedUserController::class, 'create']
        )->name('permitted-user.create');

        Route::post(
            'resource-types/{resource_type_id}/resources',
            [ResourceController::class, 'create']
        )->name('resource.create');

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            [ItemController::class, 'create']
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [ItemCategoryController::class, 'create']
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [ItemSubcategoryController::class, 'create']
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [ItemDataController::class, 'create']
        )->name('item-data.create');

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log',
            [ItemLogController::class, 'create']
        )->name('item-log.create');

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            [ItemPartialTransferController::class, 'transfer']
        );

        Route::post(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            [ItemTransferController::class, 'transfer']
        );

        Route::delete(
            'resource-types/{resource_type_id}',
            [ResourceTypeController::class, 'delete']
        )->name('resource-type.delete');

        Route::delete(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [PermittedUserController::class, 'delete']
        )->name('permitted-user.delete');

        Route::delete(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [CategoryController::class, 'delete']
        )->name('category.delete');

        Route::delete(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [SubcategoryController::class, 'delete']
        )->name('subcategory.delete');

        Route::delete(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            [ItemPartialTransferController::class, 'delete']
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceController::class, 'delete']
        )->name('resource.delete');

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [ItemController::class, 'delete']
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [ItemCategoryController::class, 'delete']
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [ItemSubcategoryController::class, 'delete']
        );

        Route::delete(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [ItemDataController::class, 'delete']
        )->name('item-data.delete');

        Route::patch(
            'resource-types/{resource_type_id}',
            [ResourceTypeController::class, 'update']
        )->name('resource-type.update');

        Route::patch(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [CategoryController::class, 'update']
        )->name('category.update');

        Route::patch(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [SubcategoryController::class, 'update']
        )->name('subcategory.update');

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceController::class, 'update']
        )->name('resource.update');

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [ItemController::class, 'update']
        );

        Route::patch(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [ItemDataController::class, 'update']
        )->name('item-data.update');
    }
);
