<?php

use App\Http\Controllers\View\CategoryController;
use App\Http\Controllers\View\CurrencyController;
use App\Http\Controllers\View\IndexController;
use App\Http\Controllers\View\ItemCategoryController;
use App\Http\Controllers\View\ItemDataController;
use App\Http\Controllers\View\ItemLogController;
use App\Http\Controllers\View\ItemSubcategoryController;
use App\Http\Controllers\View\ItemSubtypeController;
use App\Http\Controllers\View\ItemTransferController;
use App\Http\Controllers\View\ItemTypeController;
use App\Http\Controllers\View\QueueController;
use App\Http\Controllers\View\ResourceTypeItemController;
use App\Http\Controllers\View\SubcategoryController;
use App\Http\Controllers\View\ItemPartialTransferController;
use App\Http\Controllers\View\ItemController;
use App\Http\Controllers\View\PermittedUserController;
use App\Http\Controllers\View\ResourceController;
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
        // Root of the API and CHANGELOG
        Route::get(
            '',
            [IndexController::class, 'index']
        );

        Route::options(
            '',
            [IndexController::class, 'optionsIndex']
        );

        Route::get(
            'changelog',
            [\App\Http\Controllers\View\IndexController::class, 'changeLog']
        );

        Route::options(
            'changelog',
            [IndexController::class, 'optionsChangeLog']
        );

        Route::get(
            'status',
            [\App\Http\Controllers\View\IndexController::class, 'status']
        );

        Route::options(
            'status',
            [IndexController::class, 'optionsStatus']
        );

        Route::get(
            'currencies',
            [CurrencyController::class, 'index']
        );

        Route::options(
            'currencies',
            [CurrencyController::class, 'optionsIndex']
        );

        Route::get(
            'currencies/{currency_id}',
            [\App\Http\Controllers\View\CurrencyController::class, 'show']
        )->name('currency.show');

        Route::options(
            'currencies/{currency_id}',
            [CurrencyController::class, 'optionsShow']
        );

        Route::get(
            'item-types',
            [\App\Http\Controllers\View\ItemTypeController::class, 'index']
        )->name('item-type.list');

        Route::options(
            'item-types',
            [ItemTypeController::class, 'optionsIndex']
        );

        Route::get(
            'item-types/{item_type_id}',
            [ItemTypeController::class, 'show']
        )->name('item-type.show');

        Route::options(
            'item-types/{item_type_id}',
            [ItemTypeController::class, 'optionsShow']
        );

        Route::get(
            'item-types/{item_type_id}/item-subtypes',
            [ItemSubtypeController::class, 'index']
        );

        Route::options(
            'item-types/{item_type_id}/item-subtypes',
            [ItemSubtypeController::class, 'optionsIndex']
        );

        Route::get(
            'item-types/{item_type_id}/item-subtypes/{item_subtype_id}',
            [\App\Http\Controllers\View\ItemSubtypeController::class, 'show']
        )->name('item-subtype.show');

        Route::options(
            'item-types/{item_type_id}/item-subtypes/{item_subtype_id}',
            [ItemSubtypeController::class, 'optionsShow']
        );

        Route::get(
            'queue',
            [QueueController::class, 'index']
        );

        Route::options(
            'queue',
            [QueueController::class, 'optionsIndex']
        );

        Route::get(
            'queue/{queue_id}',
            [QueueController::class, 'show']
        );

        Route::options(
            'queue/{queue_id}',
            [QueueController::class, 'optionsShow']
        );

        Route::get(
            'resource-types',
            [\App\Http\Controllers\View\ResourceTypeController::class, 'index']
        )->name('resource-type.list');

        Route::options(
            'resource-types',
            [\App\Http\Controllers\View\ResourceTypeController::class, 'optionsIndex']
        )->name('resource-type.list.options');

        Route::get(
            'resource-types/{resource_type_id}',
            [\App\Http\Controllers\View\ResourceTypeController::class, 'show']
        )->name('resource-type.show');

        Route::options(
            'resource-types/{resource_type_id}',
            [\App\Http\Controllers\View\ResourceTypeController::class, 'optionsShow']
        )->name('resource-type.show.options');

        Route::get(
            'resource-types/{resource_type_id}/categories',
            [CategoryController::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/categories',
            [CategoryController::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [\App\Http\Controllers\View\CategoryController::class, 'show']
        )->name('category.show');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [CategoryController::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [\App\Http\Controllers\View\SubcategoryController::class, 'index']
        )->name('subcategory.list');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [SubcategoryController::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [\App\Http\Controllers\View\SubcategoryController::class, 'show']
        )->name('subcategory.show');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [SubcategoryController::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/items',
            [ResourceTypeItemController::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/items',
            [ResourceTypeItemController::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers',
            [ItemPartialTransferController::class, 'index']
        )->name('partial-transfers.list');

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers',
            [ItemPartialTransferController::class, 'optionsIndex']
        )->name('partial-transfers.list.options');

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            [ItemPartialTransferController::class, 'show']
        )->name('partial-transfers.show');

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            [ItemPartialTransferController::class, 'optionsShow']
        )->name('partial-transfers.show.options');

        Route::get(
            'resource-types/{resource_type_id}/permitted-users',
            [PermittedUserController::class, 'index']
        )->name('permitted-user.list');

        Route::options(
            'resource-types/{resource_type_id}/permitted-users',
            [PermittedUserController::class, 'optionsIndex']
        )->name('permitted-user.list.options');

        Route::get(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [PermittedUserController::class, 'show']
        )->name('permitted-user.show');

        Route::options(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [PermittedUserController::class, 'optionsShow']
        )->name('permitted-user.show.options');

        Route::get(
            'resource-types/{resource_type_id}/resources',
            [ResourceController::class, 'index']
        )->name('resource.list');

        Route::options(
            'resource-types/{resource_type_id}/resources',
            [ResourceController::class, 'optionsIndex']
        )->name('resource.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceController::class, 'show']
        )->name('resource.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceController::class, 'optionsShow']
        )->name('resource.show.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            [ItemController::class, 'index']
        )->name('item.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            [ItemController::class, 'optionsIndex']
        )->name('item.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [ItemController::class, 'show']
        )->name('item.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [ItemController::class, 'optionsShow']
        )->name('item.show.options');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            [ItemPartialTransferController::class, 'optionsTransfer']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            [ItemTransferController::class, 'optionsTransfer']
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [ItemCategoryController::class, 'index']
        )->name('item.categories.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [ItemCategoryController::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [ItemCategoryController::class, 'show']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [ItemCategoryController::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [ItemSubcategoryController::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [ItemSubcategoryController::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [ItemSubcategoryController::class, 'show']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [ItemSubcategoryController::class, 'optionsShow']
        );


        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [ItemDataController::class, 'index']
        )->name('item-data.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [ItemDataController::class, 'optionsIndex']
        )->name('item-data.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [ItemDataController::class, 'show']
        )->name('item-data.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [ItemDataController::class, 'optionsShow']
        )->name('item-data.show.options');


        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log',
            [ItemLogController::class, 'index']
        )->name('item-log.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log',
            [ItemLogController::class, 'optionsIndex']
        )->name('item-log.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log/{item_log_id}',
            [ItemLogController::class, 'show']
        )->name('item-log.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/log/{item_log_id}',
            [ItemLogController::class, 'optionsShow']
        )->name('item-log.show.options');


        Route::options(
            'resource-types/{resource_type_id}/transfers',
            [ItemTransferController::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers',
            [ItemTransferController::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            [ItemTransferController::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            [ItemTransferController::class, 'show']
        );

        // Request access and error logs
        Route::options(
            'request/error-log',
            [ App\Http\Controllers\View\RequestController::class, 'optionsErrorLog']
        );

        Route::get(
            'request/error-log',
            [ App\Http\Controllers\View\RequestController::class, 'errorLog']
        );

        Route::post(
            'request/error-log',
            [ App\Http\Controllers\Manage\RequestController::class, 'createErrorLog']
        );
    }
);
