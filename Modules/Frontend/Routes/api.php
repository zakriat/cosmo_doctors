<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::get('frontend', fn (Request $request) => $request->user())->name('frontend');
});

// Public API routes for booking flow
Route::prefix('api')->group(function () {
    Route::get('categories/{categoryId}/doctors', [\Modules\Frontend\Http\Controllers\ServiceController::class, 'getDoctorsByCategory'])->name('api.categories.doctors');
    Route::get('clinics/check-single', [\Modules\Frontend\Http\Controllers\ServiceController::class, 'checkSingleClinic'])->name('api.clinics.check-single');
});
