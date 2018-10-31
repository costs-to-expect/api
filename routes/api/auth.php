<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', 'PassportController@login')->prefix(Config::get('api.version.prefix'));
/**
 * Disabled because there are only currently going to be two users
 */
//Route::post('auth/register', 'PassportController@register')->prefix(Config::get('api.version.prefix'));
