<?php

use Illuminate\Support\Facades\Route;

require('api/auth.php');
require('api/category.php');
require('api/currency.php');
require('api/index.php');
require('api/item.php');
require('api/item-category.php');
require('api/item-data.php');
require('api/item-log.php');
require('api/item-partial-transfer.php');
require('api/item-subcategory.php');
require('api/item-subtype.php');
require('api/item-transfer.php');
require('api/item-type.php');
require('api/permitted-user.php');
require('api/queue.php');
require('api/request.php');
require('api/resource.php');
require('api/resource-type.php');
require('api/resource-type-item.php');
require('api/subcategory.php');


Route::fallback(function () {
    return response()->json(
        ['message' => trans('responses.invalid-route')],
        404
    );
});
