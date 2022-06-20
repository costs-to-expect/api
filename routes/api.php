<?php

require('api/auth.php');
require('api/private-routes.php');
require('api/public-routes.php');
require('api/public-summary-routes.php');

Route::fallback(function () {
    return response()->json(
        ['message' => trans('responses.invalid-route')]
    );
});
