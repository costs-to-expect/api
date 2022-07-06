<?php

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

Route::view(
    'documentation',
    'documentation',
    [
        'maintenance' => app()->isDownForMaintenance(),
        'version' => $version['version'],
        'date' => $version['release_date']
    ]
);
