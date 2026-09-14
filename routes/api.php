<?php

use App\Core\Libraries\Stateless\Static\StlStaConfig;
use App\Http\Controllers\CntAccount;
use App\Http\Controllers\CntIdle;
use App\Http\Controllers\CntSandbox;
use App\Http\Controllers\CntDevelop;
use App\Http\Controllers\CntGacha;
use App\Http\Controllers\CntItem;
use App\Http\Controllers\CntProxy;
use App\Http\Controllers\CntUser;
use Illuminate\Support\Facades\Route;

Route::domain(StlStaConfig::app()->url)->group(function () {
    // @note auth none
    Route::middleware(['mdl.response', 'mdl.transaction'])->group(function () {
        Route::prefix('account')->controller(CntAccount::class)->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');
        });
    });
    // @note auth require
    Route::middleware(['auth:sanctum', 'mdl.fake_now', 'mdl.response', 'mdl.transaction'])->group(function () {
        Route::prefix('user')->controller(CntUser::class)->group(function () {
            Route::get('info', 'info');
        });
        Route::prefix('payment')->controller(CntIdle::class)->group(function () {
            Route::post('info', 'info');
            Route::post('history', 'history');
            Route::post('charge', 'charge');
        });
        Route::prefix('item')->controller(CntItem::class)->group(function () {
            Route::get('get', 'get');
            Route::get('find', 'find');
        });
        Route::prefix('gacha')->controller(CntGacha::class)->group(function () {
            Route::get('get', 'get');
            Route::post('play', 'play');
        });
        Route::prefix('idle')->controller(CntIdle::class)->group(function () {
            Route::post('sync', 'sync');
        });
        Route::prefix('develop')->controller(CntDevelop::class)->group(function () {
            Route::post('item-add', 'itemAdd');
            Route::post('item-sub', 'itemSub');
            Route::post('set-fake-now', 'setFakeNow');
            Route::get('get-fake-now', 'getFakeNow');
            Route::post('unset-fake-now', 'unsetFakeNow');
        });
        Route::prefix('sandbox')->controller(CntSandbox::class)->group(function () {
            Route::post('set-que', 'setQue');
        });
    });
    // @note pod
    Route::middleware(['mdl.response', 'mdl.forward_pod'])->group(function () {
        Route::prefix('proxy')->controller(CntProxy::class)->group(function () {
            Route::prefix('api')->controller(CntProxy::class)->group(function () {
                Route::any('{action}', fn() => null);
            });
        });
    });
});
