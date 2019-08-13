<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/settings', '\OptimistDigital\NovaSettings\Http\Controllers\Settings@get');
Route::post('/settings', '\OptimistDigital\NovaSettings\Http\Controllers\Settings@save');
