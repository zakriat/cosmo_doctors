<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\RequestService\Http\Controllers\Backend\API\RequestServicesController;
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
    Route::get('get-request-service', [RequestServicesController::class, 'getRequestList']);
    Route::post('save-request-service', [Modules\RequestService\Http\Controllers\Backend\RequestServicesController::class, 'store']);
});
