<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomForm\Http\Controllers\Backend\CustomFormsController;

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
     *  Backend CustomForms Routes
     *
     * ---------------------------------------------------------------------
     */
    Route::group(['prefix' => 'customforms', 'as' => 'customforms.'],function () {
      
      Route::get("index_list", [CustomFormsController::class, 'index_list'])->name("index_list");
      Route::get("index_data", [CustomFormsController::class, 'index_data'])->name("index_data");
      Route::get('export', [CustomFormsController::class, 'export'])->name('export');
      Route::get('get-form-data', [CustomFormsController::class, 'getCustomForm'])->name('get-form-data');
      Route::post('store-form-data', [CustomFormsController::class, 'StoreFormData'])->name('store-form-data');

    });
    Route::resource("customforms", CustomFormsController::class);
});

