<?php
use Illuminate\Support\Facades\Route;
use Modules\Service\Http\Controllers\Backend\API\ServiceController;
use Modules\Service\Http\Controllers\Backend\API\ServicePackageController;
use Modules\Service\Http\Controllers\Backend\API\SystemServiceController;

Route::get('service-list', [ServiceController::class, 'serviceList']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('service/staff/{id}', [ServiceController::class, 'assign_employee_list']);
    Route::post('service/staff/{id}', [ServiceController::class, 'assign_employee_update']);

    Route::get('service/service-provider/{id}', [ServiceController::class, 'assign_service_provider_list']);
    Route::post('service/service-provider/{id}', [ServiceController::class, 'assign_service_provider_update']);

    // Gallery Images
    Route::get('service-gallery', [ServiceController::class, 'ServiceGallery']);
    Route::post('/gallery-images/{id}', [ServiceController::class, 'uploadGalleryImages']);

    Route::apiResource('service', ServiceController::class);
    Route::apiResource('servicePackage', ServicePackageController::class);

    Route::post('service-detail', [ServiceController::class, 'serviceDetails']);
    Route::get('search-service', [ServiceController::class, 'searchServices']);
});

// Route::get('get-system-service',[SystemServiceController::class, 'SystemServiceList']);
// Route::get('system-service-details',[SystemServiceController::class, 'SystemServiceDetails']);
// Route::get('get-category-list',[SystemServiceController::class, 'CategoryList']);
// Route::get('get-service-list',[SystemServiceController::class, 'ServiceList']);
// Route::get('get-service-detail',[SystemServiceController::class, 'ServiceDetails']);
// Route::get('get-clinics',[SystemServiceController::class, 'ClinicsList']);
// Route::get('get-doctors',[SystemServiceController::class, 'EmployeeList']);





