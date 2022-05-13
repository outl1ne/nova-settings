<?php

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

Route::namespace('\Outl1ne\NovaSettings\Http\Controllers')->group(function () {
    Route::prefix('nova-vendor/nova-settings')->group(function () {
        Route::get('/settings', 'SettingsController@get')->name('nova-settings.get');
        Route::post('/settings', 'SettingsController@save')->name('nova-settings.save');
    });

    Route::delete('/nova-api/nova-settings/{path}/field/{fieldName}', 'SettingsController@deleteImage');
});
