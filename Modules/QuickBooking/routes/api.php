<?php

use Illuminate\Support\Facades\Route;
use Modules\QuickBooking\Http\Controllers\Backend\QuickBookingsController;

Route::group(['prefix' => 'quick-booking'], function () {
    Route::get('service-provider-list', [QuickBookingsController::class, 'service_provider_list']);
    Route::get('slot-time-list', [QuickBookingsController::class, 'slot_time_list']);
    Route::get('services-list', [QuickBookingsController::class, 'services_list']);
    Route::post('store', [QuickBookingsController::class, 'create_booking'])->name('api.quick_bookings.store');
    Route::get('system-service-list', [QuickBookingsController::class, 'system_service_list']);
    Route::get('clinic-list', [QuickBookingsController::class, 'clinic_list']);
    Route::get('doctor-list', [QuickBookingsController::class, 'doctor_list']);
    Route::post('verify_customer', [QuickBookingsController::class, 'verify_customer']);



    
});
