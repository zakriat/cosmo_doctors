<?php

use Illuminate\Support\Facades\Route;
use Modules\FrontendSetting\Http\Controllers\FrontendSettingController;

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

Route::group(['prefix' => 'app', 'as' => '', 'middleware' => ['auth','auth_check']], function () {
    Route::get('frontend-setting/{page?}', [FrontendSettingController::class, 'frontendSettings'])->name('frontend_setting.index');
    Route::post('/layout-frontend-page', [FrontendSettingController::class, 'layoutPage'])->name('layout_frontend_page');
    Route::post('/landing-layout-page', [FrontendSettingController::class, 'landingLayoutPage'])->name('landing_layout_page');
    Route::post('/landing-page-settings-updates', [FrontendSettingController::class, 'landingpageSettingsUpdates'])->name('landing_page_settings_updates');
    Route::post('/get-landing-layout-page-config', [FrontendSettingController::class, 'getLandingLayoutPageConfig'])->name('getLandingLayoutPageConfig');
    Route::post('/header-page-settings', [FrontendSettingController::class, 'headingpagesettings'])->name('heading_page_settings');
    Route::post('/footer-page-settings', [FrontendSettingController::class, 'footerpagesettings'])->name('footer_page_settings');


    
    Route::get('frontend/landing-page', [FrontendSettingController::class, 'landingPageLayout'])->name('frontendsetting.landingpage');
    Route::get('frontend/header-page', [FrontendSettingController::class, 'headerPage'])->name('frontendsetting.headerpage');
    Route::get('frontend/footer-page', [FrontendSettingController::class, 'footerPage'])->name('frontendsetting.footerpage');
    Route::post('frontend-settings/update', 'FrontendSettingController@updateSettings')
        ->name('frontend.settings.update');
});

// Route::get('/landing-layout', [FrontendSettingController::class, 'landingPageLayout'])->name('landing_layout_page');
Route::post('/landing-layout', [FrontendSettingController::class, 'getSection'])->name('get_section');
