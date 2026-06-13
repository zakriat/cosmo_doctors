<?php

use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\BackupController;
use App\Http\Controllers\Backend\IncidenceReportController;
use App\Http\Controllers\Backend\NotificationsController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\DoctorHolidayController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermission;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AudioFileController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HolidayController;
use Modules\Frontend\Http\Controllers\FrontendController;
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

// Auth Routes
require __DIR__ . '/auth.php';

// Audio file serving route (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('audio/serve/{id}', [AudioFileController::class, 'serve'])->name('audio.serve');
});

Route::get('storage-link', function () {
    return Artisan::call('storage:link');
});

// ========================================
// CACHE CLEARING ROUTE - Visit: yourdomain.com/clear-cache
// ========================================
Route::get('clear-cache', function () {
    try {
        // Clear all types of cache
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        // Also clear compiled files
        Artisan::call('clear-compiled');
        
        // Try to clear event cache (if exists)
        try {
            Artisan::call('event:clear');
        } catch (\Exception $e) {
            // Ignore if event:clear doesn't exist
        }
        
        $message = '✓ All Cache Cleared Successfully!';
        $details = 'Cleared: Application Cache, Config, Routes, Views, Compiled Files';
        $status = 'success';
        
    } catch (\Exception $e) {
        $message = '✗ Error Clearing Cache';
        $details = 'Error: ' . $e->getMessage();
        $status = 'error';
    }
    
    return '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cache Cleared</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: white;
                padding: 50px;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                text-align: center;
                max-width: 600px;
                width: 100%;
            }
            h1 {
                color: ' . ($status == 'success' ? '#28a745' : '#dc3545') . ';
                font-size: 2.5em;
                margin-bottom: 20px;
            }
            .details {
                font-size: 1.2em;
                color: #555;
                margin: 20px 0;
                line-height: 1.6;
            }
            .timestamp {
                color: #999;
                font-size: 0.9em;
                margin: 15px 0;
            }
            .btn {
                display: inline-block;
                margin-top: 30px;
                padding: 15px 40px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                border-radius: 50px;
                font-size: 1.1em;
                font-weight: 600;
                transition: transform 0.2s, box-shadow 0.2s;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            }
            .icon {
                font-size: 4em;
                margin-bottom: 20px;
            }
            .cache-list {
                text-align: left;
                margin: 20px auto;
                max-width: 400px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 10px;
            }
            .cache-list li {
                padding: 8px 0;
                color: #666;
                list-style: none;
            }
            .cache-list li:before {
                content: "✓ ";
                color: #28a745;
                font-weight: bold;
                margin-right: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="icon">' . ($status == 'success' ? '🚀' : '⚠️') . '</div>
            <h1>' . $message . '</h1>
            <div class="details">' . $details . '</div>
            <ul class="cache-list">
                <li>Application Cache</li>
                <li>Configuration Cache</li>
                <li>Route Cache</li>
                <li>View/Blade Cache</li>
                <li>Compiled Files</li>
                <li>Event Cache</li>
            </ul>
            <div class="timestamp">⏰ Cleared at: ' . now()->format('Y-m-d H:i:s') . '</div>
            <a href="/" class="btn">← Back to Home</a>
        </div>
    </body>
    </html>';
});



Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');




Route::get('/home', function () {

    if (auth()->user()->hasRole('vendor')) {

        return redirect(RouteServiceProvider::VENDOR_LOGIN_REDIRECT);
    } else if (auth()->user()->hasRole('doctor')) {

        return redirect(RouteServiceProvider::DOCTOR_LOGIN_REDIRECT);
    }
    // else if (auth()->user()->hasRole('user')) {

    //     return redirect(RouteServiceProvider::USER_LOGIN_REDIRECT);
    // }
    else if (auth()->user()->hasRole('receptionist')) {

        return redirect(RouteServiceProvider::RECEPTIONIST_LOGIN_REDIRECT);
    } else if (auth()->user()->hasRole('nurse')) {

        return redirect(RouteServiceProvider::NURSE_LOGIN_REDIRECT);
    } else if (auth()->user()->hasRole('user')) {

        return redirect(RouteServiceProvider::USER_LOGIN_REDIRECT);
    } else {
        return redirect(RouteServiceProvider::HOME);
    }
})->middleware('auth');

Route::group(['middleware' => ['auth']], function () {
    Route::get('notification-list', [NotificationsController::class, 'notificationList'])->name('notification.list');
    Route::get('notification-counts', [NotificationsController::class, 'notificationCounts'])->name('notification.counts');
    Route::delete('notification-remove/{id}', [NotificationsController::class, 'notificationRemove'])->name('notification.remove');
});

Route::group(['prefix' => 'app', 'middleware' => ['auth', 'auth_check']], function () {
    // Language Switch
    Route::get('language/{language}', [LanguageController::class, 'switch'])->name('language.switch');
    Route::post('set-user-setting', [BackendController::class, 'setUserSetting'])->name('backend.setUserSetting');

    Route::get('/ajax-list', [BackendController::class, 'getAjaxList'])->name('ajax-list');

    Route::group(['as' => 'backend.', 'middleware' => ['auth']], function () {
        Route::post('update-player-id', [UserController::class, 'update_player_id'])->name('update-player-id');
        Route::get('get_search_data', [SearchController::class, 'get_search_data'])->name('get_search_data');

        // Sync Role & Permission
        Route::get('/permission-role', [RolePermission::class, 'index'])->name('permission-role.list')->middleware('password.confirm');
        Route::post('/permission-role/store/{role_id}', [RolePermission::class, 'store'])->name('permission-role.store');
        Route::get('/permission-role/reset/{role_id}', [RolePermission::class, 'reset_permission'])->name('permission-role.reset');
        // Role & Permissions Crud
        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);

        Route::group(['prefix' => 'module', 'as' => 'module.'], function () {
            Route::get('index_data', [ModuleController::class, 'index_data'])->name('index_data');
            Route::post('update-status/{id}', [ModuleController::class, 'update_status'])->name('update_status');
        });

        Route::resource('module', ModuleController::class);

        /*
          *
          *  Settings Routes
          *
          * ---------------------------------------------------------------------
          */
        Route::group(['middleware' => ['auth_check']], function () {
            Route::get('settings/{vue_capture?}', [SettingController::class, 'index'])->name('settings')->where('vue_capture', '^(?!storage).*$');
            Route::get('settings-data', [SettingController::class, 'index_data']);
            Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
            Route::post('setting-update', [SettingController::class, 'update'])->name('setting.update');
            Route::get('clear-cache', [SettingController::class, 'clear_cache'])->name('clear-cache');
            Route::post('verify-email', [SettingController::class, 'verify_email'])->name('verify-email');
        });

        /*
        *
        *  Notification Routes
        *
        * ---------------------------------------------------------------------
        */
        Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
            Route::get('/', [NotificationsController::class, 'index'])->name('index');
            Route::get('/markAllAsRead', [NotificationsController::class, 'markAllAsRead'])->name('markAllAsRead');
            Route::delete('/deleteAll', [NotificationsController::class, 'deleteAll'])->name('deleteAll');
            Route::get('/{id}', [NotificationsController::class, 'show'])->name('show');
        });

        Route::group(['prefix' => 'custom-form', 'as' => 'custom-form.'], function () {
            Route::get('/', function () {
                return redirect('app/settings#/customform');
            })->name('index');
        });

        /* Incidence Report */
        Route::group(['prefix' => 'incidence', 'as' => 'incidence.', 'middleware' => ['check_admin_role']], function () {
            Route::get('/', [IncidenceReportController::class, 'index'])->name('index');

            Route::get("index_list", [IncidenceReportController::class, 'index_list'])->name("index_list");
            Route::get("index_data", [IncidenceReportController::class, 'index_data'])->name("index_data");
            Route::get('export', [IncidenceReportController::class, 'export'])->name('export');
            Route::get('get-reply/{id}', [IncidenceReportController::class, 'getReply'])->name('getReply');
            Route::post('reply', [IncidenceReportController::class, 'reply'])->name('reply');
            Route::post('bulk-action', [IncidenceReportController::class, 'bulk_action'])->name('bulk_action');

            Route::post('/update-status/{id}', [IncidenceReportController::class, 'updateStatus'])->name('updateStatus');
        });

        /*
        *
        *  Backup Routes
        *
        * ---------------------------------------------------------------------
        */
        Route::group([
            'prefix' => 'backups',
            'as' => 'backups.',
            'middleware' => ['permission:view_backup']
        ], function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::get('/create', [BackupController::class, 'create'])->name('create');
            Route::get('/create-db-bk', [BackupController::class, 'createDBBk'])->name('create-db-bk');
            Route::get('/download/{file_name}', [BackupController::class, 'download'])->name('download');
            Route::delete('/delete/{file_name}', [BackupController::class, 'delete'])->name('delete');
            Route::get('index_data', [BackupController::class, 'index_data'])->name('index_data');

            Route::get('/logs', [BackupController::class, 'activityLogIndex'])->name('logs');
            Route::get('/logs/view/{id}', [BackupController::class, 'activityLogView'])->name('logs.view');
            Route::get('activity_log_index_data', [BackupController::class, 'activity_log_index_data'])->name('activity_log_index_data');
        });

        Route::get('commission-revenue', [ReportsController::class, 'commission_revenue'])->name('reports.commission-revenue');
        Route::get('commission-revenue-index-data', [ReportsController::class, 'commission_revenue_index_data'])->name('reports.commission-revenue.index_data');

        Route::get('appointment-overview', [ReportsController::class, 'appointment_overview'])->name('reports.appointment-overview');
        Route::get('appointment-overview-index-data', [ReportsController::class, 'appointment_overview_index_data'])->name('reports.appointment-overview.index_data');

        Route::get('clinic-overview', [ReportsController::class, 'clinic_overview'])->name('reports.clinic-overview');
        Route::get('clinic-overview-index-data', [ReportsController::class, 'clinic_overview_index_data'])->name('reports.clinic-overview.index_data');
        Route::get('clinic-overview-date-range', [ReportsController::class, 'clinic_overview_date_range'])->name('reports.clinic-overview.date_range');

        Route::get('doctor-payout-report', [ReportsController::class, 'doctor_payout_report'])->name('reports.doctor-payout-report');
        Route::get('doctor-payout-report-index-data', [ReportsController::class, 'doctor_payout_report_index_data'])->name('reports.doctor-payout-report.index_data');

        Route::get('vendor-payout-report', [ReportsController::class, 'vendor_payout_report'])->name('reports.vendor-payout-report');
        Route::get('vendor-payout-report-index-data', [ReportsController::class, 'vendor_payout_report_index_data'])->name('reports.vendor-payout-report.index_data');
    });

    /*
    *
    * Backend Routes
    * These routes need view-backend permission
    * --------------------------------------------------------------------
    */
    Route::group(['as' => 'backend.', 'middleware' => ['auth']], function () {
        /**
         * Backend Dashboard
         * Namespaces indicate folder structure.
         */
        Route::get('/', [BackendController::class, 'index'])->name('home')->middleware('check_admin_role');
        Route::get('/daterange/{daterange}', [BackendController::class, 'daterange'])->name('daterange');
        Route::get('/vendor-daterange/{daterange}', [BackendController::class, 'vendorDateRange'])->name('vendor-daterange');
        Route::get('/doctor-dashboard', [BackendController::class, 'doctorDashboard'])->name('doctor-dashboard');
        Route::get('/get-appointments', [BackendController::class, 'getAppointments'])->name('get-appointments');
        Route::get('/get-clinic-appointments', [BackendController::class, 'getClinicAppointments'])->name('get-clinic-appointments');
        Route::get('/vendor-dashboard', [BackendController::class, 'vendorDashboard'])->name('vendor-dashboard');
        Route::get('/receptionist-dashboard', [BackendController::class, 'receptionistDashboard'])->name('receptionist-dashboard');
        Route::get('/nurse-dashboard', [BackendController::class, 'nurseDashboard'])->name('nurse-dashboard');
        Route::post('set-current-service-providers/{service_provider_id}', [BackendController::class, 'setCurrentServiceProvider'])->name('set-current-service-provider');
        Route::post('reset-service-providers', [BackendController::class, 'resetServiceProvider'])->name('reset-service-provider');
        Route::get('google-auth', [BackendController::class, 'googleAuth'])->name('google-auth');
        Route::get('/get_revnue_chart_data/{type}', [BackendController::class, 'getRevenuechartData']);

        Route::resource('holidays', HolidayController::class);
        Route::get('get_pickers', [HolidayController::class, 'get_pickers'])->name('get_pickers');
        Route::get('delete_pickers', [HolidayController::class, 'destroy'])->name('delete_pickers');


        Route::resource('doctorholidays', DoctorHolidayController::class);
        Route::get('get_doctorpickers', [DoctorHolidayController::class, 'get_doctorpickers'])->name('get_doctorpickers');
        Route::get('delete_doctorpickers', [DoctorHolidayController::class, 'destroy'])->name('delete_doctorpickers');

        Route::group(['prefix' => ''], function () {
            Route::get('dashboard', [BackendController::class, 'index'])->name('dashboard');

            /*
            *
            *  Users Routes
            *
            * ---------------------------------------------------------------------
            */
            Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                Route::get('user-list', [UserController::class, 'user_list'])->name('user_list');
                Route::get('emailConfirmationResend/{id}', [UserController::class, 'emailConfirmationResend'])->name('emailConfirmationResend');
                Route::post('create-customer', [UserController::class, 'create_customer'])->name('create_customer');
                Route::post('information', [UserController::class, 'update'])->name('information');
                Route::post('change-password', [UserController::class, 'change_password'])->name('change_password');
            });
        });
        Route::get('my-profile/{vue_capture?}', [UserController::class, 'myProfile'])->name('my-profile')->where('vue_capture', '^(?!storage).*$');
        Route::get('my-info', [UserController::class, 'authData'])->name('authData');
        Route::post('my-profile/change-password', [UserController::class, 'change_password'])->name('change_password');
        Route::get('app-configuration', [App\Http\Controllers\Backend\API\SettingController::class, 'appConfiguraton']);
        Route::get('data-configuration', [App\Http\Controllers\Backend\API\SettingController::class, 'Configuraton']);
        Route::post('store-access-token', [SettingController::class, 'storeToken']);
        Route::post('token-revoke', [SettingController::class, 'revokeToken']);
    });

    Route::post('/auth/google', [SettingController::class, 'googleId']);
    Route::get('callback', [SettingController::class, 'handleGoogleCallback']);
    Route::post('/store-access-token', [SettingController::class, 'storeToken']);
    Route::get('google-key', [SettingController::class, 'googleKey']);
    Route::get('download-json', [SettingController::class, 'downloadJson']);
});

Route::get('/razorpay/payment/{order_id}', [\Modules\Frontend\Http\Controllers\AppointmentController::class, 'showRazorpayPaymentPage'])->name('razorpay.payment.page');
