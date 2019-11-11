<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

$version = Config::get('api.app.version');

Route::view(
    '',
    'welcome',
    [
        'maintenance' => app()->isDownForMaintenance(),
        'version' => $version['version'],
        'date' => $version['release_date']
    ]
);


