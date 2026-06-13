<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Clinic\Http\Controllers\API\ClinicesController;
use Modules\Clinic\Http\Controllers\API\DoctorController;
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

Route::get('get-service-list', [ClinicesController::class, 'ServiceList']);
Route::get('get-doctor-list', [DoctorController::class, 'DoctorList']);
Route::get('get-category-list', [ClinicesController::class, 'CategoryList']);
Route::get('get-clinic-list', [ClinicesController::class, 'ClinicList']);
Route::get('get-clinic-gallery', [ClinicesController::class, 'ClinicGallery']);
Route::get('get-system-service', [ClinicesController::class, 'SystemServiceList']);
Route::get('get-service-details', [ClinicesController::class, 'ServiceList']);
Route::get('get-clinic-details', [ClinicesController::class, 'ClinicList']);
Route::get('get-doctor-details', [DoctorController::class, 'DoctorList']);
Route::get('get-rating', [DoctorController::class, 'getRating']);

Route::get('get-time-slots', [Modules\Clinic\Http\Controllers\DoctorController::class, 'availableSlot']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('get-categories', [ClinicesController::class, 'CategoryList']);
    Route::post('save-clinic-category', [Modules\Clinic\Http\Controllers\ClinicsCategoryController::class, 'store']);
    Route::post('update-clinic-category/{id}', [Modules\Clinic\Http\Controllers\ClinicsCategoryController::class, 'update']);
    Route::post('delete-clinic-category/{id}', [Modules\Clinic\Http\Controllers\ClinicsCategoryController::class, 'destroy']);

    Route::get('get-services', [ClinicesController::class, 'ServiceList']);
    Route::post('save-clinic-service', [Modules\Clinic\Http\Controllers\ClinicsServiceController::class, 'store']);
    Route::post('update-clinic-service/{id}', [Modules\Clinic\Http\Controllers\ClinicsServiceController::class, 'update']);
    Route::post('delete-clinic-service/{id}', [Modules\Clinic\Http\Controllers\ClinicsServiceController::class, 'destroy']);
    Route::get('service-detail', [Modules\Clinic\Http\Controllers\ClinicsServiceController::class, 'ServiceDetails']);

    Route::get('get-clinics', [ClinicesController::class, 'ClinicList']);

    Route::post('save-clinic', [Modules\Clinic\Http\Controllers\ClinicesController::class, 'store']);
    Route::post('update-clinic/{id}', [Modules\Clinic\Http\Controllers\ClinicesController::class, 'update']);
    Route::post('delete-clinic/{id}', [Modules\Clinic\Http\Controllers\ClinicesController::class, 'destroy']);
    Route::get('clinic-session', [ClinicesController::class, 'clinicSession']);


    Route::post('save-clinic-gallery', [ClinicesController::class, 'uploadGalleryImages']);

    Route::post('save-clinic-session', [Modules\Clinic\Http\Controllers\ClinicSessionController::class, 'store']);
    Route::get('get-clinic-session-list', [ClinicesController::class, 'clinicSessionList']);

    Route::get('get-doctors', [DoctorController::class, 'DoctorList']);
    Route::get('doctor-details', [DoctorController::class, 'DoctorList']);
    Route::post('save-doctor', [Modules\Clinic\Http\Controllers\DoctorController::class, 'store']);
    Route::post('update-doctor/{id}', [Modules\Clinic\Http\Controllers\DoctorController::class, 'update']);
    Route::post('delete-doctor/{id}', [Modules\Clinic\Http\Controllers\DoctorController::class, 'destroy']);
    Route::get('get-doctors-session_list', [DoctorController::class, 'DoctorSessionList']);
    // Route::post('save-doctor-session', [Modules\Clinic\Http\Controllers\DoctorSessionController::class, 'store']);
    Route::post('save-doctor-session/{id}', [Modules\Clinic\Http\Controllers\DoctorSessionController::class, 'update']);
    Route::get('doctor-commission-list', [DoctorController::class, 'doctorCommissionList']);
    Route::get('doctor-payout-history', [DoctorController::class, 'doctorPayoutHistory']);
    Route::get('doctor-session', [DoctorController::class, 'doctorSession']);
    Route::post('assign-doctor', [DoctorController::class, 'assignDoctor']);
    Route::post('assign-doctor-service', [DoctorController::class, 'assignDoctorService']);


    Route::post('save-rating', [DoctorController::class, 'saveRating'])->name('save-rating');
    Route::post('delete-rating', [DoctorController::class, 'deleteRating'])->name('delete-rating');

    Route::get('specialization-list', [ClinicesController::class, 'specializationList']);

    Route::get('get-patients', [DoctorController::class, 'patientList']);
    Route::get('get-patient-details', [DoctorController::class, 'patientList']);

    Route::post('save-receptionist', [Modules\Clinic\Http\Controllers\ReceptionistController::class, 'store']);
    Route::get('get-receptionists', [DoctorController::class, 'receptionistList']);
    Route::get('get-receptionist-details', [DoctorController::class, 'receptionistList']);
    Route::post('update-receptionist/{id}', [Modules\Clinic\Http\Controllers\ReceptionistController::class, 'update']);
    Route::post('delete-receptionist/{id}', [Modules\Clinic\Http\Controllers\ReceptionistController::class, 'destroy']);

});


// NEW: Category-based booking routes
Route::get('services/{serviceId}/categories', [Modules\Frontend\Http\Controllers\ServiceController::class, 'getServiceCategories']);
Route::get('categories/{categoryId}/doctors', [Modules\Frontend\Http\Controllers\ServiceController::class, 'getDoctorsByCategory']);

// NEW: Doctor-category management routes (admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('services/{serviceId}/categories-for-doctor', [Modules\Clinic\Http\Controllers\DoctorController::class, 'getCategoriesByService']);
});
