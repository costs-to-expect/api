<?php

use App\Http\Controllers\CategoryView;
use App\Http\Controllers\CurrencyView;
use App\Http\Controllers\IndexView;
use App\Http\Controllers\ItemCategoryView;
use App\Http\Controllers\ItemDataView;
use App\Http\Controllers\ItemSubcategoryView;
use App\Http\Controllers\ItemSubtypeView;
use App\Http\Controllers\ItemTransferView;
use App\Http\Controllers\ItemTypeView;
use App\Http\Controllers\QueueView;
use App\Http\Controllers\RequestManage;
use App\Http\Controllers\RequestView;
use App\Http\Controllers\ResourceTypeItemView;
use App\Http\Controllers\SubcategoryView;
use App\Http\Controllers\ToolView;
use App\Http\Controllers\ItemPartialTransferView;
use App\Http\Controllers\ItemView;
use App\Http\Controllers\PermittedUserView;
use App\Http\Controllers\ResourceView;
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
            [IndexView::class, 'index']
        );

        Route::options(
            '',
            [IndexView::class, 'optionsIndex']
        );

        Route::get(
            'changelog',
            [\App\Http\Controllers\IndexView::class, 'changeLog']
        );

        Route::options(
            'changelog',
            [IndexView::class, 'optionsChangeLog']
        );

        Route::get(
            'currencies',
            [CurrencyView::class, 'index']
        );

        Route::options(
            'currencies',
            [CurrencyView::class, 'optionsIndex']
        );

        Route::get(
            'currencies/{currency_id}',
            [\App\Http\Controllers\CurrencyView::class, 'show']
        )->name('currency.show');

        Route::options(
            'currencies/{currency_id}',
            [CurrencyView::class, 'optionsShow']
        );

        Route::get(
            'item-types',
            [\App\Http\Controllers\ItemTypeView::class, 'index']
        )->name('item-type.list');

        Route::options(
            'item-types',
            [ItemTypeView::class, 'optionsIndex']
        );

        Route::get(
            'item-types/{item_type_id}',
            [ItemTypeView::class, 'show']
        )->name('item-type.show');

        Route::options(
            'item-types/{item_type_id}',
            [ItemTypeView::class, 'optionsShow']
        );

        Route::get(
            'item-types/{item_type_id}/item-subtypes',
            [ItemSubtypeView::class, 'index']
        );

        Route::options(
            'item-types/{item_type_id}/item-subtypes',
            [ItemSubtypeView::class, 'optionsIndex']
        );

        Route::get(
            'item-types/{item_type_id}/item-subtypes/{item_subtype_id}',
            [\App\Http\Controllers\ItemSubtypeView::class, 'show']
        )->name('item-subtype.show');

        Route::options(
            'item-types/{item_type_id}/item-subtypes/{item_subtype_id}',
            [ItemSubtypeView::class, 'optionsShow']
        );

        Route::get(
            'queue',
            [QueueView::class, 'index']
        );

        Route::options(
            'queue',
            [QueueView::class, 'optionsIndex']
        );

        Route::get(
            'queue/{queue_id}',
            [QueueView::class, 'show']
        );

        Route::options(
            'queue/{queue_id}',
            [QueueView::class, 'optionsShow']
        );

        Route::get(
            'resource-types',
            [\App\Http\Controllers\ResourceTypeView::class, 'index']
        )->name('resource-type.list');

        Route::options(
            'resource-types',
            [\App\Http\Controllers\ResourceTypeView::class, 'optionsIndex']
        )->name('resource-type.list.options');

        Route::get(
            'resource-types/{resource_type_id}',
            [\App\Http\Controllers\ResourceTypeView::class, 'show']
        )->name('resource-type.show');

        Route::options(
            'resource-types/{resource_type_id}',
            [\App\Http\Controllers\ResourceTypeView::class, 'optionsShow']
        )->name('resource-type.show.options');

        Route::get(
            'resource-types/{resource_type_id}/categories',
            [CategoryView::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/categories',
            [CategoryView::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [\App\Http\Controllers\CategoryView::class, 'show']
        )->name('category.show');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}',
            [CategoryView::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [\App\Http\Controllers\SubcategoryView::class, 'index']
        )->name('subcategory.list');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [SubcategoryView::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [\App\Http\Controllers\SubcategoryView::class, 'show']
        )->name('subcategory.show');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            [SubcategoryView::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/items',
            [ResourceTypeItemView::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/items',
            [ResourceTypeItemView::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers',
            [ItemPartialTransferView::class, 'index']
        )->name('partial-transfers.list');

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers',
            [ItemPartialTransferView::class, 'optionsIndex']
        )->name('partial-transfers.list.options');

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            [ItemPartialTransferView::class, 'show']
        )->name('partial-transfers.show');

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            [ItemPartialTransferView::class, 'optionsShow']
        )->name('partial-transfers.show.options');

        Route::get(
            'resource-types/{resource_type_id}/permitted-users',
            [PermittedUserView::class, 'index']
        )->name('permitted-user.list');

        Route::options(
            'resource-types/{resource_type_id}/permitted-users',
            [PermittedUserView::class, 'optionsIndex']
        )->name('permitted-user.list.options');

        Route::get(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [PermittedUserView::class, 'show']
        )->name('permitted-user.show');

        Route::options(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [PermittedUserView::class, 'optionsShow']
        )->name('permitted-user.show.options');

        Route::get(
            'resource-types/{resource_type_id}/resources',
            [ResourceView::class, 'index']
        )->name('resource.list');

        Route::options(
            'resource-types/{resource_type_id}/resources',
            [ResourceView::class, 'optionsIndex']
        )->name('resource.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceView::class, 'show']
        )->name('resource.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [ResourceView::class, 'optionsShow']
        )->name('resource.show.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            [ItemView::class, 'index']
        )->name('item.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            [ItemView::class, 'optionsIndex']
        )->name('item.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [ItemView::class, 'show']
        )->name('item.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [ItemView::class, 'optionsShow']
        )->name('item.show.options');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            [ItemPartialTransferView::class, 'optionsTransfer']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            [ItemTransferView::class, 'optionsTransfer']
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [ItemCategoryView::class, 'index']
        )->name('item.categories.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            [ItemCategoryView::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [ItemCategoryView::class, 'show']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            [ItemCategoryView::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [ItemSubcategoryView::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            [ItemSubcategoryView::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [ItemSubcategoryView::class, 'show']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            [ItemSubcategoryView::class, 'optionsShow']
        );


        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [ItemDataView::class, 'index']
        )->name('item-data.list');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data',
            [ItemDataView::class, 'optionsIndex']
        )->name('item-data.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [ItemDataView::class, 'show']
        )->name('item-data.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/data/{key}',
            [ItemDataView::class, 'optionsShow']
        )->name('item-data.show.options');


        Route::options(
            'resource-types/{resource_type_id}/transfers',
            [ItemTransferView::class, 'optionsIndex']
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers',
            [ItemTransferView::class, 'index']
        );

        Route::options(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            [ItemTransferView::class, 'optionsShow']
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            [ItemTransferView::class, 'show']
        );

        // Request access and error logs
        Route::options(
            'request/error-log',
            [RequestView::class, 'optionsErrorLog']
        );

        Route::get(
            'request/error-log',
            [RequestView::class, 'errorLog']
        );

        Route::post(
            'request/error-log',
            [RequestManage::class, 'createErrorLog']
        );

        Route::options(
            'tools/cache',
            [ToolView::class, 'optionsCache']
        );
    }
);
