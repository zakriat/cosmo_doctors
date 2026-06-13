<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\CustomForm\Http\Controllers\Backend\CustomFormsController;
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

Route::get('get-customform-list', [CustomFormsController::class, 'getCustomFormList']);
Route::get('get-customform-data-list', [CustomFormsController::class, 'getCustomFormDataList']);


Route::group(['middleware' => 'auth:sanctum'], function () {

});
