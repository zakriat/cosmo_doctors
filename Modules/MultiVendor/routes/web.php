<?php

use Illuminate\Support\Facades\Route;
use Modules\MultiVendor\Http\Controllers\Backend\MultiVendorsController;

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
     *  Backend MultiVendors Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'multivendors', 'as' => 'multivendors.'],function () {
      Route::get("index_list", [MultiVendorsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [MultiVendorsController::class, 'index_data'])->name("index_data");
      Route::get('check-email', [MultiVendorsController::class, 'checkEmail'])->name('check_email');
      Route::get('check-mobile', [MultiVendorsController::class, 'checkMobile'])->name('check_mobile');
      Route::get('get-all-mobiles', [MultiVendorsController::class, 'getAllMobiles'])->name('get_all_mobiles');
      Route::get('test-mobile/{mobile}', [MultiVendorsController::class, 'testMobileCheck'])->name('test_mobile');
      Route::get('export', [MultiVendorsController::class, 'export'])->name('export');
      Route::post('block-vendor/{id}', [MultiVendorsController::class, 'block_vendor'])->name('block-vendor');
      Route::post('verify-vendor/{id}', [MultiVendorsController::class, 'verify_vendor'])->name('verify-vendor');
      Route::post('change-password', [MultiVendorsController::class, 'change_password'])->name('change_password');
      Route::post('bulk-action', [MultiVendorsController::class, 'bulk_action'])->name('bulk_action');
      Route::post('update-status/{id}', [MultiVendorsController::class, 'update_status'])->name('update_status');
      Route::get('view', [MultiVendorsController::class, 'view'])->name('view');

    });
    Route::resource("multivendors", MultiVendorsController::class);
});

