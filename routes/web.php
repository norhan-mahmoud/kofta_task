<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'items','as' => 'items.','controller' => App\Http\Controllers\ItemController::class], function () {
    Route::get('/','index')->name('index');
    Route::post('/store','store')->name('store');
});

Route::group(['prefix' => 'cook','as' => 'cook.','controller' => App\Http\Controllers\CookController::class], function () {
    Route::get('/','index')->name('index');
    Route::post('/store','store')->name('store');
});

//supply routes
Route::group(['prefix' => 'supplies','as' => 'supplies.','controller' => App\Http\Controllers\SupplyController::class], function () {
    Route::get('/','index')->name('index');
    Route::post('/store','store')->name('store');
});

Route::group(['prefix' => 'orders','as' => 'orders.','controller' => App\Http\Controllers\OrderController::class], function () {
    Route::get('/','index')->name('index');
    Route::post('/store','store')->name('store');
    Route::get('/lifecycle','lifecycleForm')->name('lifecycleForm');
    Route::get('/traceback','tracebackForm')->name('tracebackForm');
    Route::get('/search','search')->name('search');
    Route::get('/traceback-search','getTracebackSearch')->name('traceback-search');


});


Route::post('/reset-factory', [App\Http\Controllers\HomeController::class, 'resetFactory'])->name('reset.factory');
