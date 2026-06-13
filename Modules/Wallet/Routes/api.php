<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\API\walletcontroller;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('get-patient-wallet',[walletcontroller::class,'getPatientWallet']);
    Route::get('get-wallet-history',[walletcontroller::class,'getWalletHistory']);
});
