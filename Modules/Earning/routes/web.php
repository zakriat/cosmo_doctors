<?php

use Illuminate\Support\Facades\Route;
use Modules\Earning\Http\Controllers\Backend\EarningsController;
use App\Http\Controllers\SearchController;
use Modules\Earning\Http\Controllers\Backend\VendorEarningsController;

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
     *  Backend Earnings Routes
     *
     * ---------------------------------------------------------------------
     */

    Route::group(['prefix' => 'earnings', 'as' => 'earnings.'],function () {
      Route::get('index_list', [EarningsController::class, 'index_list'])->name('index_list');
      Route::get('index_data', [EarningsController::class, 'index_data'])->name('index_data');
      Route::get('export', [EarningsController::class, 'export'])->name('export');
      Route::get('get_search_data', [SearchController::class, 'get_search_data'])->name('get_search_data');
      Route::get('get-employee-commissions', [EarningsController::class, 'get_employee_commissions'])->name('get-employee-commissions');
    });
    Route::resource('earnings', EarningsController::class);

    Route::group(['prefix' => 'vendor-earnings', 'as' => 'vendor-earnings.'], function () {
      Route::get('index_data', [VendorEarningsController::class, 'index_data'])->name('index_data');
      Route::get('export', [VendorEarningsController::class, 'export'])->name('export');
      
    });
    Route::resource('vendor-earnings', VendorEarningsController::class);
});

