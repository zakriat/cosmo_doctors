<?php

use Illuminate\Support\Facades\Route;
use Modules\Frontend\Http\Controllers\FrontendController;
use Modules\Frontend\Http\Controllers\CategoryController;
use Modules\Frontend\Http\Controllers\ServiceController;
use Modules\Frontend\Http\Controllers\ClinicController;
use Modules\Frontend\Http\Controllers\DoctorController;
use Modules\Frontend\Http\Controllers\BlogController;
use Modules\Frontend\Http\Controllers\AppointmentController;
use Modules\Frontend\Http\Controllers\DraftAppointmentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Backend\NotificationsController;
use Modules\Frontend\Http\Controllers\Auth\UserController;
use Modules\Frontend\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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

Route::get('/login', [UserController::class, 'login'])->name('login-page');
Route::get('/register', [UserController::class, 'registration'])->name('register-page');
Route::get('/forgot-password', [UserController::class, 'forgotpassword'])->name('forgot-password');
Route::post('forgot-password', [UserController::class, 'store'])->name('password.emailuser');
Route::get('reset-password/{token}', [UserController::class, 'create'])->name('password.reset');
Route::post('reset-password', [UserController::class, 'storepassword'])->name('password.update');
Route::post('/forgot-password-link', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::controller(UserController::class)->group(function () {
    Route::post('user-login', 'loginstore')->name('user-login');

    Route::get('multi-factor-auth/{id?}', 'multiFactorAuth')->name('multi-factor-auth');
    Route::post('2fa', 'completeRegistration')->name('2fa');
    
    // Login with Google
    Route::get('/auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('/auth/google/callback', 'handleGoogleCallback')->name('auth.google.callback');
});
Route::get('language/{language}', [LanguageController::class, 'switch'])->name('language.switch');

Route::middleware(['check.header.menu'])->group(function () {
    Route::get('/categories', [CategoryController::class, 'categoriesList'])->name('categories');
    Route::get('/services', [ServiceController::class, 'servicesList'])->name('services');
    Route::get('/clinics', [ClinicController::class, 'clinicsList'])->name('clinics');
    Route::get('/doctors', [DoctorController::class, 'doctorsList'])->name('doctors');

    ### incidence report route front end
    Route::get('/incidence', [FrontendController::class, 'incidenceReport'])->name('incidence.index');
    Route::get('incidence_index_data', [FrontendController::class, 'index_data'])->name('incidence.index_data');
    Route::post('/incidence-save', [FrontendController::class, 'incidenceSave'])->name('incidence.store');
    Route::post('/changestatus', [FrontendController::class, 'changeStatus'])->name('changestatus');
 
    
    Route::get('/service-details/{id}', [ServiceController::class, 'serviceDetails'])->name('service-details');
    Route::get('/clinic-details/{id}', [ClinicController::class, 'clinicDetails'])->name('clinic-details');
    Route::get('/doctor-details/{id}', [DoctorController::class, 'doctorDetails'])->name('doctor-details');
});

Route::get('/getClinicsByService', [ClinicController::class, 'getClinicsByService'])->name('getClinicsByService');
Route::get('/api/services/{service}/categories', [ServiceController::class, 'getServiceCategories'])->name('api.service.categories');
Route::get('/api/categories/{category}/doctors', [ServiceController::class, 'getCategoryDoctors'])->name('api.category.doctors');
Route::get('/blogs', [BlogController::class, 'blogsList'])->name('blogs');
Route::get('/reviews', [DoctorController::class, 'reviewsList'])->name('reviews');
Route::post('/cancel-appointment/{id}', [AppointmentController::class, 'cancelAppointment'])->name('cancel-appointment');
Route::get('/get-services-by-clinic', [ClinicController::class, 'getServicesByClinic'])->name('getServicesByClinic');


Route::get('/search', [FrontendController::class, 'searchList'])->name('search');
Route::get('/get-search', [FrontendController::class, 'getSearch'])->name('getSearchData');
Route::get('/faq', [FrontendController::class, 'faqList'])->name('faq');
Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('about-us');
Route::get('/contact-us', [FrontendController::class, 'contactUs'])->name('contact-us');


Route::get('/blog-details/{id}', [BlogController::class, 'blogDetails'])->name('blog-details');

Route::group(['middleware' => ['auth','user_check']], function () {
    // Patient Dashboard Routes - Protected with patient-specific authentication and data access middleware
    Route::group(['middleware' => ['patient_auth', 'patient_data']], function () {
        Route::get('/patient-dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');
        Route::get('/patient/dashboard/appointments', [PatientDashboardController::class, 'appointments'])->name('patient.dashboard.appointments');
        Route::get('/patient/dashboard/prescriptions', [PatientDashboardController::class, 'prescriptions'])->name('patient.dashboard.prescriptions');
        Route::get('/patient/dashboard/triage', [PatientDashboardController::class, 'triageRecords'])->name('patient.dashboard.triage');
        Route::get('/patient/dashboard/medical-records', [PatientDashboardController::class, 'medicalRecords'])->name('patient.dashboard.medical-records');
        Route::get('/patient/dashboard/stats', [PatientDashboardController::class, 'dashboardStats'])->name('patient.dashboard.stats');
        Route::get('/patient/dashboard/encounter/{id}', [PatientDashboardController::class, 'encounterDetails'])->name('patient.dashboard.encounter.details');
    });
    
    Route::get('/account-setting', [UserController::class, 'accountSetting'])->name('account-setting');
    Route::get('/edit-profile', [UserController::class, 'editProfile'])->name('edit-profile');
    Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('update-profile');
    Route::post('/update-profile-image', [UserController::class, 'updateProfileImage'])->name('update-profile-image');
    Route::get('/appointment-list', [AppointmentController::class, 'appointmentList'])->name('appointment-list')->middleware('check.header.menu');
    Route::get('/blood-tests', [AppointmentController::class, 'bloodTestsList'])->name('patient.blood-tests')->middleware('check.header.menu');
    Route::get('/blood-tests/{id}/download-report', [AppointmentController::class, 'downloadBloodTestReport'])->name('patient.blood-tests.download');
    Route::get('/wallet-history', [UserController::class, 'walletHistory'])->name('wallet-history');
    Route::get('/wallet-history-index-data', [UserController::class, 'walletHistoryIndexData'])->name('wallet-history.index_data');

    Route::post('/account/password/update', [UserController::class, 'updatePassword'])->name('account.password.update');
    Route::delete('/account/delete', [UserController::class, 'deleteAccount'])->name('account.delete');
    Route::post('user-logout', [UserController::class, 'destroy'])->name('user-logout');
   
    Route::get('/encounter-list', [AppointmentController::class, 'encounterList'])->name('encounter-list');
    Route::post('/save-appointment', [AppointmentController::class, 'saveAppointment'])->name('saveAppointment');
    Route::get('/user-notifications', [UserController::class, 'userNotifications'])->name('user-notifications');
    Route::get('/user-notifications-index-data', [UserController::class, 'userNotifications_indexData'])->name('user-notifications.index_data');
    Route::get('booking/{id}', [ServiceController::class, 'booking'])->name('booking');
    Route::post('/transcribe-audio', [ServiceController::class, 'transcribeAudio'])->name('transcribe-audio');
    Route::post('/transcribe-audio-enhanced', [ServiceController::class, 'transcribeAudioEnhanced'])->name('transcribe-audio-enhanced');
    Route::post('/pay-now', [AppointmentController::class, 'payNow'])->name('pay-now');
    Route::get('/appointment-details/{id}', [AppointmentController::class, 'appointmentDetails'])->name('appointment-details');
    Route::post('/appointments/{id}/update-medical-history', [AppointmentController::class, 'updateMedicalHistory'])->name('appointments.update-medical-history');
    Route::get('/other-patients-list', [AppointmentController::class, 'otherpatientlist'])->name('other-patients.list');
    Route::post('other-patients', [AppointmentController::class, 'otherpatient'])->name('other-patients.store');
    Route::get('/manage-profile', [AppointmentController::class, 'manageProfile'])->name('manage-profile');
    Route::get('/manage-profile-data', [AppointmentController::class, 'manageProfile_index_data'])->name('manage-profile-data');
    Route::get('/other-patients/{id}/edit', [AppointmentController::class, 'editOtherPatient'])->name('other-patients.edit');
    Route::post('/other-patients/{id}', [AppointmentController::class, 'updateOtherPatient'])->name('other-patients.update');
    Route::delete('/other-patients/{id}', [AppointmentController::class, 'destroyOtherPatient'])->name('other-patients.destroy');

    // Draft Appointment Routes
    Route::post('/api/draft-appointments', [DraftAppointmentController::class, 'saveDraft'])->name('draft-appointments.save');
    Route::get('/api/draft-appointments/{id}', [DraftAppointmentController::class, 'getDraft'])->name('draft-appointments.get');
    Route::get('/api/draft-appointments', [DraftAppointmentController::class, 'getUserDrafts'])->name('draft-appointments.list');
    Route::delete('/api/draft-appointments/{id}', [DraftAppointmentController::class, 'deleteDraft'])->name('draft-appointments.delete');
    Route::post('/api/draft-appointments/cleanup', [DraftAppointmentController::class, 'deleteDraftAfterBooking'])->name('draft-appointments.cleanup');

});


Route::get('category_index_data', [CategoryController::class, 'index_data'])->name('category.index_data');
Route::get('service_index_data', [ServiceController::class, 'index_data'])->name('service.index_data');
Route::get('clinic_index_data', [ClinicController::class, 'index_data'])->name('clinic.index_data');
Route::get('doctor_index_data', [DoctorController::class, 'index_data'])->name('doctor.index_data');
Route::get('blog_index_data', [BlogController::class, 'index_data'])->name('blog.index_data');
Route::get('appointment_index_data', [AppointmentController::class, 'index_data'])->name('appointment.index_data');
Route::get('encounter_index_data', [AppointmentController::class, 'encounter_index_data'])->name('encounter.index_data');

Route::post('/get-payment-data', [AppointmentController::class, 'getPaymentData'])->name('payment.data');
Route::post('/slot-time-list', [AppointmentController::class, 'slot_time_list'])->name('slot_time_list');
// Route::post('/save-appointment', [AppointmentController::class, 'saveAppointment'])->name('saveAppointment');
Route::get('/payment/success', [AppointmentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('notification-list', [NotificationsController::class, 'notificationList'])->name('notification.list');
Route::get('notification-counts', [NotificationsController::class, 'notificationCounts'])->name('notification.counts');
Route::post('/check-wallet-balance', [AppointmentController::class, 'checkWalletBalance'])->name('check.wallet.balance');
Route::post('/random-slot', [AppointmentController::class, 'randomSlot'])->name('random_slot');
Route::get('/check-booking-status/{serviceId}/{doctorId}/{clinicId}', [DoctorController::class, 'checkBookingStatus'])->name('check.booking.status');
Route::get('download_invoice', [AppointmentController::class, 'downloadPDf'])->name('download_invoice');
