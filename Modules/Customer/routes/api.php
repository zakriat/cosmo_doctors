<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Backend\API\CustomersController;



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
// Route::get('get-Patient', [CustomersController::class, 'getPatientList']); 
// Route::post('add-Patient', [CustomersController::class, 'store']);
// Route::get('Patient-Details', [CustomersController::class, 'customerDetails']);
// Route::get('edit-patient/{id}', [CustomersController::class, 'edit']);
// Route::post('update-patient/{id}', [CustomersController::class, 'update']);
// Route::delete('delete-patient/{id}', [CustomersController::class, 'destroy']);


Route::get('profile-details', [CustomersController::class, 'profileDetails']);
Route::post('add-patient-member', [CustomersController::class, 'addOtherMember']);
Route::get('other-members-list', [CustomersController::class, 'getOtherMembersList']);
Route::post('delete-other_member/{id}',[CustomersController::class, 'deleteOtherMember']);
});



