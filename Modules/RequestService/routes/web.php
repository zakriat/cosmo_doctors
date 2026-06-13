<?php

use Illuminate\Support\Facades\Route;
use Modules\RequestService\Http\Controllers\Backend\RequestServicesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the 'web' middleware group. Now create something great!
|
*/
/*
*
* Backend Routes
*
* --------------------------------------------------------------------
*/
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
     *  Backend RequestServices Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'requestservices', 'as' => 'requestservices.'],function () {
      Route::get('index_list', [RequestServicesController::class, 'index_list'])->name('index_list');
      Route::get('index_data', [RequestServicesController::class, 'index_data'])->name('index_data');
      Route::post('update-status/{id}', [RequestServicesController::class, 'update_status'])->name('update_status');
      Route::post('approve/{id}', [RequestServicesController::class, 'approve'])->name('approve');
      Route::post('decline/{id}', [RequestServicesController::class, 'decline'])->name('decline');
    });
    Route::resource('requestservices', RequestServicesController::class);
    Route::get('datatable_view', [RequestServicesController::class, 'datatable_view'])->name('datatable_view');
});

