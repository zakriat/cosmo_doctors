<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\BlogController;
use Modules\Clinic\Http\Controllers\ClinicsServiceController;
use Modules\Clinic\Http\Controllers\ClinicesController;
use Modules\Customer\Http\Controllers\Backend\CustomersController;
use Modules\Tax\Http\Controllers\Backend\TaxesController;
use Modules\Clinic\Http\Controllers\DoctorController;
use Modules\Appointment\Http\Controllers\Backend\AppointmentsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group([], function () {
//     Route::resource('blog', BlogController::class)->names('blog');
// });


Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth','auth_check']], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Backend FAQS Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'blog', 'as' => 'blog.'],function () {
        Route::post('blog-bulk-action', [BlogController::class, 'bulk_action'])->name('bulk_action');
        Route::get('blog-index-data', [BlogController::class, 'index_data'])->name('index_data');
        Route::post('blog-action', [BlogController::class, 'action'])->name('action');
        Route::post('update-status/{id}', [BlogController::class, 'update_status'])->name('update_status');
        Route::post('blog/{id}', [BlogController::class, 'destroy'])->name('destroy');
        Route::get('blog/{id}/remove-media/{media_id}', [BlogController::class, 'removeMedia'])->name('remove-media');

        Route::get('services/index_list', [ClinicsServiceController::class, 'index_list'])->name("blogs.services.index_list");
        Route::get('clinics/index_list', [ClinicesController::class, 'index_list'])->name('blogs.clinics.index_list');
        Route::get('customers/index_list', [CustomersController::class, 'index_list'])->name('blogs.customers.index_list');
        Route::get('tax/index_list', [TaxesController::class, 'index_list'])->name('blogs.tax.index_list');
        Route::get('appointment/other-patientlist', [AppointmentsController::class, 'otherpatientlist'])->name('blogs.other_patientlist');
        Route::get('doctor/index_list', [DoctorController::class, 'index_list'])->name('blogs.doctor.index_list');
        Route::get('services/service-price', [ClinicsServiceController::class, 'service_price'])->name('blogs.service_price');
        Route::get('doctor/get-available-slot', [DoctorController::class, 'availableSlot'])->name('blogs.availableSlot');
    });
    Route::resource('blog', BlogController::class);
});                                                                                                                                                                                             