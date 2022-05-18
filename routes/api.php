<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;

use Illuminate\Support\Facades\Route;

/**
 * API VERSION 1
 */
Route::prefix('v1')->as('v1.')->group(function(){

    /**
     * Authentication Route
     */
    Route::group(['prefix'=>'auth', 'as'=>'auth.user.'], function(){
        Route::post('register', [RegisterController::class,'register'])->name('register');
        Route::post('verify_user',[RegisterController::class,'verifyPhoneNumber'])->name('verify');
        Route::post('login',[LoginController::class,'login'])->name('login');

        Route::group(['middleware'=>'auth:user'], function(){
            Route::post('login_with_token',[LoginController::class,'loginWithToken'])->middleware('auth:user')->name('login_with_token');
            Route::get('logout',[LoginController::class,'logout'])->name('logout');
        });
    });

});
