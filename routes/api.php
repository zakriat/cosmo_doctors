<?php

use App\Http\Controllers\Auth\API\AuthController;
use App\Http\Controllers\Backend\API\AddressController;
use App\Http\Controllers\Backend\API\DashboardController;
use App\Http\Controllers\Backend\API\DBBackupController;
use App\Http\Controllers\Backend\API\NotificationsController;
use App\Http\Controllers\Backend\API\SettingController;
use App\Http\Controllers\Backend\BackupController;
use App\Http\Controllers\IncidenceController;
use App\Http\Controllers\SearchController;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('user-detail', [AuthController::class, 'userDetails']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('api.register');
    Route::post('login', 'login');
    Route::post('generate-qr', 'generateQR');
    Route::post('verify', 'verify');
    Route::post('otp-resend', 'otpSend');
    Route::post('otp-verify', 'verifyOtp');
    Route::post('social-login', 'socialLogin');
    Route::post('forgot-password', 'forgotPassword');
    Route::get('logout', 'logout');
});
Route::post('check-contact', [AuthController::class, 'checkContact']);
Route::post('check-email', [AuthController::class, 'checkEmail']);
Route::post('/store-access-token', [SettingController::class, 'storeToken']);
Route::post('/token-revoke', [SettingController::class, 'revokeToken']);

Route::get('dashboard-detail', [DashboardController::class, 'DashboardDetail']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    ### database backup 
    Route::get('backup', [DBBackupController::class, 'index']);
    Route::post('download-backup-files', [DBBackupController::class, 'downloadBkFiles']);

    Route::apiResource('setting', SettingController::class);
    Route::apiResource('notification', NotificationsController::class);
    Route::get('notification-list', [NotificationsController::class, 'notificationList']);
    Route::get('gallery-list', [DashboardController::class, 'globalGallery']);
    Route::get('search-list', [DashboardController::class, 'searchList']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);

    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('delete-account', [AuthController::class, 'deleteAccount']);

    Route::post('add-address', [AddressController::class, 'store']);
    Route::get('address-list', [AddressController::class, 'AddressList']);
    Route::get('remove-address', [AddressController::class, 'RemoveAddress']);
    Route::post('edit-address', [AddressController::class, 'EditAddress']);

    Route::get('vendor-dashboard-list', [DashboardController::class, 'VendorDashboardDetail']);
    Route::get('doctor-dashboard-list', [DashboardController::class, 'doctorDashboardDetail']);
    Route::get('receptionist-dashboard-list', [DashboardController::class, 'receptionistDashboardDetail']);
    Route::get('advance_filter', [SearchController::class, 'advanceFilter'])->name('advance_filter');

    Route::apiResource('incidence-list', IncidenceController::class);
    Route::post('incidence/change-status/{id}', [IncidenceController::class, 'changeStatus']);
    Route::post('incidence-save', [IncidenceController::class, 'store']);
    Route::post('update-incident-status/{id}', [IncidenceController::class, 'updateIncidentStatus']);
    // Route::post('incidence/{id}', [IncidenceController::class, 'update']);
});
Route::get('app-configuration', [SettingController::class, 'appConfiguraton']);
Route::get('download-json', [SettingController::class, 'downloadJson']);

// Enhanced booking - Get doctors by category
Route::get('categories/{categoryId}/doctors', [\Modules\Frontend\Http\Controllers\ServiceController::class, 'getDoctorsByCategory']);
